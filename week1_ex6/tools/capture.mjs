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
const profile = await mkdtemp(resolve(tmpdir(), "clinic-chrome-"));
const browser = spawn(process.env.CHROME_BIN || "/usr/bin/google-chrome", [
  "--headless=new", "--no-sandbox", "--disable-dev-shm-usage",
  "--force-color-profile=srgb", "--hide-scrollbars", "--disable-lcd-text", "--font-render-hinting=none",
  "--remote-debugging-port=0", "--user-data-dir=" + profile, "about:blank"
], { stdio: ["ignore", "ignore", "pipe"] });

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

function send(method, params = {}, sessionId) {
  const id = ++requestId;
  return new Promise((resolveCommand, reject) => {
    const timer = setTimeout(() => {
      pending.delete(id);
      reject(new Error("CDP timeout: " + method));
    }, 45000);
    pending.set(id, { resolve: resolveCommand, reject, timer });
    socket.send(JSON.stringify({ id, method, params, sessionId }));
  });
}

try {
  const { targetId } = await send("Target.createTarget", { url: "about:blank" });
  const { sessionId } = await send("Target.attachToTarget", { targetId, flatten: true });
  await Promise.all(["Page.enable", "Runtime.enable", "Network.enable", "Log.enable"].map(method => send(method, {}, sessionId)));
  const reports = [];
  const viewports = process.argv.includes("--desktop") ? [[1300, 1000]] : [
    [1300, 1000], [1024, 768], [1023, 768], [1022, 768],
    [992, 768], [991, 768], [990, 768],
    [768, 1024], [767, 1024], [766, 1024], [390, 844], [375, 812], [844, 390]
  ];

  for (const [width, height] of viewports) {
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
        brokenImages: Array.from(document.images).filter(image => !image.complete || !image.naturalWidth).map(image => image.src),
        boxes: {
          header: document.querySelector('.header').getBoundingClientRect(),
          nav: document.querySelector('.nav').getBoundingClientRect(),
          hero: document.querySelector('.hero').getBoundingClientRect(),
          main: document.querySelector('.main-content').getBoundingClientRect(),
          sidebar: document.querySelector('.sidebar').getBoundingClientRect(),
          footer: document.querySelector('.footer').getBoundingClientRect()
        }
      })`,
      returnByValue: true
    }, sessionId);
    const parsed = JSON.parse(result.value);
    reports.push(parsed);

    if (width === 1300 || width === 390 || width === 375) {
      const metrics = await send("Page.getLayoutMetrics", {}, sessionId);
      const screenshot = await send("Page.captureScreenshot", {
        format: "png",
        captureBeyondViewport: true,
        clip: { x: 0, y: 0, width, height: Math.ceil(metrics.cssContentSize.height), scale: 1 }
      }, sessionId);
      await mkdir(resolve(projectRoot, "screenshots"), { recursive: true });
      await writeFile(resolve(projectRoot, "screenshots/screenshot_desktop_" + width + ".png"), Buffer.from(screenshot.data, "base64"));
    }
  }

  const version = await send("Browser.getVersion");
  const report = { browser: version.product, timestamp: new Date().toISOString(), errors, reports };
  await writeFile(resolve(projectRoot, "screenshots/browser_report.json"), JSON.stringify(report, null, 2) + "\n");
  console.info(JSON.stringify({
    browser: report.browser, errors,
    reports: reports.map(({viewport,overflow,brokenImages}) => ({viewport,overflow,brokenImages}))
  }, null, 2));

  if (errors.length || reports.some(report => report.overflow || report.brokenImages.length)) {
    process.exitCode = 1;
  }
} finally {
  socket.close();
  browser.kill();
  server.close();
}
