import { spawn } from "node:child_process";
import { createServer } from "node:http";
import { readFile, writeFile, mkdir, mkdtemp, rm } from "node:fs/promises";
import { join, extname, resolve } from "node:path";
import { fileURLToPath } from "node:url";

const root = resolve(process.env.EX_REVIEW_ROOT || fileURLToPath(new URL("../", import.meta.url)));
const output = process.argv[2] || root;
const server = createServer(async (request, response) => {
  try {
    const path = resolve(root, "." + decodeURIComponent(new URL(request.url, "http://localhost").pathname));
    if (!path.startsWith(root + "/")) throw new Error("Invalid path");
    response.setHeader("Content-Type", {
      ".html": "text/html", ".css": "text/css", ".webp": "image/webp"
    }[extname(path)] || "application/octet-stream");
    response.end(await readFile(path));
  } catch {
    response.writeHead(404);
    response.end();
  }
});
await new Promise(resolveListen => server.listen(0, "127.0.0.1", resolveListen));
const profile = await mkdtemp("/tmp/ex123-chrome-");
const browser = spawn(process.env.CHROME_BIN || "/usr/bin/google-chrome", [
  "--headless=new", "--no-sandbox", "--disable-dev-shm-usage", "--force-color-profile=srgb",
  "--disable-lcd-text", "--font-render-hinting=none", "--remote-debugging-port=0",
  "--user-data-dir=" + profile, "about:blank"
], { stdio: ["ignore", "ignore", "pipe"] });
const endpoint = await new Promise((resolveEndpoint, reject) => {
  let outputText = "";
  const timer = setTimeout(() => reject(new Error("Browser startup timeout")), 15000);
  browser.stderr.on("data", chunk => {
    outputText += chunk;
    const match = outputText.match(/DevTools listening on (ws:\/\/[^\s]+)/);
    if (match) {
      clearTimeout(timer);
      resolveEndpoint(match[1]);
    }
  });
});
const socket = new WebSocket(endpoint);
await new Promise(resolveOpen => socket.addEventListener("open", resolveOpen, { once: true }));
let nextId = 0;
const pending = new Map();
const errors = [];
socket.addEventListener("message", event => {
  const message = JSON.parse(event.data);
  if (message.id) {
    const task = pending.get(message.id);
    if (task) {
      clearTimeout(task.timer);
      pending.delete(message.id);
      if (message.error) task.reject(message.error);
      else task.resolve(message.result);
    }
  }
  if (message.method === "Runtime.exceptionThrown" || message.method === "Network.loadingFailed") errors.push(message);
  if (message.method === "Log.entryAdded" && message.params.entry.level === "error") errors.push(message.params.entry);
  if (message.method === "Network.responseReceived" && message.params.response.status >= 400) {
    errors.push({ url: message.params.response.url, status: message.params.response.status });
  }
});

/* Send a CDP command (string, object, string); return its result as a Promise<object>.
   CDPコマンド（文字列、オブジェクト、文字列）を送信し、結果をPromise<object>で返す。 */
function send(method, params = {}, sessionId) {
  const id = ++nextId;
  return new Promise((resolveCommand, reject) => {
    const timer = setTimeout(() => reject(new Error(method + " timeout")), 15000);
    pending.set(id, { resolve: resolveCommand, reject, timer });
    socket.send(JSON.stringify({ id, method, params, sessionId }));
  });
}

try {
  const { targetId } = await send("Target.createTarget", { url: "about:blank" });
  const { sessionId } = await send("Target.attachToTarget", { targetId, flatten: true });
  await Promise.all(["Page.enable", "Runtime.enable", "Network.enable", "Log.enable"].map(method => send(method, {}, sessionId)));
  for (const [exercise, desktop] of [[1, [1031, 585]], [2, [363, 774]], [3, [672, 700]]]) {
    const reports = [];
    const directory = join(output, `week1_ex${exercise}`, "screenshots");
    await mkdir(directory, { recursive: true });
    const viewports = process.env.EX_REVIEW_DESKTOP_ONLY ? [desktop] : [desktop, [320, 740], [375, 812], [390, 844], [599, 800], [600, 800], [601, 800], [708, 900], [709, 900], [768, 900], [844, 390], [1024, 768], [1440, 900]];
    for (const [width, height] of viewports) {
      const errorStart = errors.length;
      await send("Page.navigate", { url: "about:blank" }, sessionId);
      await send("Emulation.setDeviceMetricsOverride", { width, height, deviceScaleFactor: 1, mobile: width <= 390 }, sessionId);
      await send("Page.navigate", { url: `http://127.0.0.1:${server.address().port}/week1_ex${exercise}/index.html` }, sessionId);
      await send("Runtime.evaluate", {
        expression: "new Promise(resolve => document.readyState === 'complete' ? resolve() : addEventListener('load', resolve, {once:true})).then(() => document.fonts.ready)", awaitPromise: true
      }, sessionId);
      const { result } = await send("Runtime.evaluate", {
        expression: `JSON.stringify((() => {
          const elements = [...document.querySelectorAll('body *')];
          const boxes = Object.fromEntries(elements.filter(element => element.className).map(element => {
            const box = element.getBoundingClientRect();
            return [element.className, {x:box.x, y:box.y, width:box.width, height:box.height}];
          }));
          const visible = elements.filter(element => !element.closest('.profile-card__scroll-box') && !element.matches('.visually-hidden'));
          const overflowingElements = visible.filter(element => {
            const box = element.getBoundingClientRect();
            return box.width && (box.right > innerWidth + 1 || box.left < -1);
          }).map(element => element.className);
          const clippedText = visible.filter(element => element.children.length === 0 && element.textContent.trim() && !element.matches('input,textarea')).filter(element => element.scrollWidth > element.clientWidth + 1 && getComputedStyle(element).display !== 'inline').map(element => element.className);
          const brokenImages = [...document.images].filter(image => !image.complete || !image.naturalWidth).map(image => image.src);
          const scrollBox = document.querySelector('.profile-card__scroll-box');
          let internalScroll = null;
          if (scrollBox) {
            scrollBox.focus();
            scrollBox.scrollLeft = 20;
            scrollBox.scrollTop = 10;
            internalScroll = {focusable:document.activeElement === scrollBox, horizontal:scrollBox.scrollWidth <= scrollBox.clientWidth || scrollBox.scrollLeft > 0, vertical:scrollBox.scrollHeight <= scrollBox.clientHeight || scrollBox.scrollTop > 0};
            scrollBox.scrollLeft = 0;
            scrollBox.scrollTop = 0;
            scrollBox.blur();
          }
          const controls = [...document.querySelectorAll('input,textarea')].map(control => ({name:control.name, labeled:control.labels.length > 0}));
          return {viewport:[innerWidth, innerHeight], overflow:document.documentElement.scrollWidth > innerWidth, overflowingElements, clippedText, brokenImages, internalScroll, controls, elements:elements.length, boxes};
        })())`, returnByValue: true
      }, sessionId);
      const report = { ...JSON.parse(result.value), errors: errors.slice(errorStart) };
      reports.push(report);
      if (width === desktop[0] || width === 375 || width === 320) {
        const metrics = await send("Page.getLayoutMetrics", {}, sessionId);
        const screenshot = await send("Page.captureScreenshot", {
          format: "png", captureBeyondViewport: true,
          clip: {x:0, y:0, width, height:Math.max(height, Math.ceil(metrics.cssContentSize.height)), scale:1}
        }, sessionId);
        await writeFile(join(directory, `review_${width}.png`), Buffer.from(screenshot.data, "base64"));
      }
    }
    await writeFile(join(directory, "review_browser.json"), JSON.stringify(reports, null, 2) + "\n");
    const failures = reports.filter(report => report.overflow || report.overflowingElements.length || report.clippedText.length || report.brokenImages.length || report.errors.length || report.controls.some(control => !control.labeled) || (report.internalScroll && Object.values(report.internalScroll).some(value => !value)));
    console.log(JSON.stringify({exercise, viewports:reports.length, failures:failures.map(({boxes, ...report}) => report)}));
    if (failures.length) process.exitCode = 1;
  }
} finally {
  socket.close();
  const closed = new Promise(resolveExit => browser.once("exit", resolveExit));
  browser.kill();
  server.close();
  await closed;
  await rm(profile, { recursive: true, force: true, maxRetries: 5, retryDelay: 100 });
}
