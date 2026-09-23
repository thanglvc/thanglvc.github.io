import { spawn } from "node:child_process";
import { createServer } from "node:http";
import { mkdtemp, mkdir, writeFile } from "node:fs/promises";
import { tmpdir } from "node:os";
import { resolve } from "node:path";
import { fileURLToPath } from "node:url";
import { serveRequest } from "../gulpfile.js";

const projectRoot = fileURLToPath(new URL("../", import.meta.url));
const server = createServer(serveRequest);
await new Promise(resolveListen => server.listen(0, "127.0.0.1", resolveListen));
const pageUrl = "http://127.0.0.1:" + server.address().port + "/";
const profile = await mkdtemp(resolve(tmpdir(), "shop-pixel-chrome-"));
const browser = spawn(process.env.CHROME_BIN || "/usr/bin/google-chrome", [
  "--headless=new", "--no-sandbox", "--disable-dev-shm-usage",
  "--force-color-profile=srgb", "--hide-scrollbars", "--disable-lcd-text", "--font-render-hinting=none",
  "--remote-debugging-port=0", "--user-data-dir=" + profile, "about:blank"
], { stdio: ["ignore", "ignore", "pipe"] });

/** Wait for Chrome's isolated debugging endpoint. No parameters; returns Promise<string>. */
function getEndpoint() {
  return new Promise((resolveEndpoint, reject) => {
    let output = "";
    const timer = setTimeout(() => reject(new Error("Chrome startup timed out: " + output)), 15000);
    browser.stderr.on("data", chunk => {
      output += chunk.toString();
      const match = output.match(/DevTools listening on (ws:\/\/[^\s]+)/);
      if (match) {
        clearTimeout(timer);
        resolveEndpoint(match[1]);
      }
    });
    browser.once("exit", code => {
      clearTimeout(timer);
      reject(new Error("Chrome exited " + code + ": " + output));
    });
  });
}

const endpoint = await getEndpoint();
const socket = new WebSocket(endpoint);
await new Promise(resolveOpen => socket.addEventListener("open", resolveOpen, { once: true }));
let requestId = 0;
const pending = new Map();
const errors = [];
socket.addEventListener("message", event => {
  const message = JSON.parse(event.data);
  if (message.id) {
    const task = pending.get(message.id);
    if (task) {
      pending.delete(message.id);
      clearTimeout(task.timer);
      if (message.error) task.reject(new Error(JSON.stringify(message.error)));
      else task.resolve(message.result);
    }
  }
  if (message.method === "Runtime.exceptionThrown" || message.method === "Network.loadingFailed") {
    errors.push(message);
  }
  if (message.method === "Network.responseReceived" && message.params.response.status >= 400) {
    errors.push({ url: message.params.response.url, status: message.params.response.status });
  }
  if (message.method === "Log.entryAdded" && message.params.entry.level === "error") {
    errors.push(message.params.entry);
  }
});

/**
 * Send a Chrome DevTools command.
 * @param {string} method - CDP method.
 * @param {object} params - Method arguments.
 * @param {string} [sessionId] - Page session.
 * @returns {Promise<object>} The CDP result.
 */
function send(method, params = {}, sessionId) {
  const id = ++requestId;
  return new Promise((resolveCommand, reject) => {
    const timer = setTimeout(() => {
      pending.delete(id);
      reject(new Error("CDP timeout: " + method));
    }, 20000);
    pending.set(id, { resolve: resolveCommand, reject, timer });
    socket.send(JSON.stringify({ id, method, params, sessionId }));
  });
}

try {
  const { targetId } = await send("Target.createTarget", { url: "about:blank" });
  const { sessionId } = await send("Target.attachToTarget", { targetId, flatten: true });
  await Promise.all(["Page.enable", "Runtime.enable", "Network.enable", "Log.enable"].map(method => send(method, {}, sessionId)));
  const reports = [];
  const viewports = process.argv.includes("--desktop") ? [[1440, 1000]] : [
    [1440, 1000], [1024, 768], [1023, 768], [1022, 768],
    [768, 1024], [767, 1024], [766, 1024], [390, 844], [375, 812], [844, 390]
  ];
  for (const [width, height] of viewports) {
    // Resize an empty page so srcset fetches from the previous size are not canceled.
    await send("Page.navigate", { url: "about:blank" }, sessionId);
    await send("Emulation.setDeviceMetricsOverride", {
      width, height, deviceScaleFactor: 1, mobile: false
    }, sessionId);
    await send("Page.navigate", { url: pageUrl }, sessionId);
    await send("Runtime.evaluate", {
      expression: "new Promise(resolve => { if (document.readyState === 'complete') resolve(); else window.addEventListener('load', resolve, {once:true}); }).then(() => document.fonts.ready).then(() => Promise.all(Array.from(document.images, image => image.decode().catch(() => {}))))",
      awaitPromise: true
    }, sessionId);
    const { result } = await send("Runtime.evaluate", {
      expression: `JSON.stringify({
        viewport: [innerWidth, innerHeight],
        overflow: document.documentElement.scrollWidth > innerWidth,
        fonts: [400,500,700].map(weight => document.fonts.check(weight + ' 16px Satoshi')),
        brokenImages: Array.from(document.images).filter(image => !image.complete || !image.naturalWidth).map(image => image.src),
        cards: document.querySelectorAll('.review-card').length,
        products: document.querySelectorAll('.product-card').length,
        textBoxes: Array.from(document.querySelectorAll('.product-card__price, .product-card__original-price, .product-card__discount, .review-card__name, .reviews__count')).map(element => {
          const box = element.getBoundingClientRect();
          return {text:element.textContent,x:box.x,y:box.y,width:box.width,height:box.height};
        }),
        boxes: Object.fromEntries(['.product-tabs','.reviews__toolbar','.reviews__list','.review-card','.review-card__author','.review-card__text','.review-card__date','.reviews__footer','.related-products__heading','.related-products__heading-image','.related-products__list','.product-card__picture','.product-card__title','.product-card__rating','.product-card__prices'].map(selector => {
          const box = document.querySelector(selector).getBoundingClientRect();
          return [selector, {x:box.x,y:box.y,width:box.width,height:box.height}];
        }))
      })`,
      returnByValue: true
    }, sessionId);
    reports.push(JSON.parse(result.value));
    if (width === 1440 || width === 390) {
      const metrics = await send("Page.getLayoutMetrics", {}, sessionId);
      const screenshot = await send("Page.captureScreenshot", {
        format: "png",
        captureBeyondViewport: true,
        clip: { x: 0, y: 0, width, height: Math.ceil(metrics.cssContentSize.height), scale: 1 }
      }, sessionId);
      await mkdir(resolve(projectRoot, "screenshots"), { recursive: true });
      await writeFile(resolve(projectRoot, "screenshots/browser_" + width + ".png"), Buffer.from(screenshot.data, "base64"));
    }
  }
  const version = await send("Browser.getVersion");
  const report = { browser: version.product, timestamp: new Date().toISOString(), errors, reports };
  await writeFile(resolve(projectRoot, "screenshots/browser_report.json"), JSON.stringify(report, null, 2) + "\n");
  console.info(JSON.stringify({
    browser: report.browser, errors,
    reports: reports.map(({viewport,overflow,fonts,brokenImages}) => ({viewport,overflow,fonts,brokenImages})),
    desktopBoxes: reports[0].boxes
  }, null, 2));
  if (errors.length || reports.some(report => report.overflow || report.brokenImages.length || report.fonts.some(loaded => !loaded))) {
    process.exitCode = 1;
  }
} finally {
  socket.close();
  browser.kill();
  server.close();
}
