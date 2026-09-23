import { createServer } from "node:http";
import { readFile, mkdir, writeFile } from "node:fs/promises";
import { extname, resolve, sep } from "node:path";
import { fileURLToPath } from "node:url";
import gulp from "gulp";
import * as sass from "sass";

const projectRoot = fileURLToPath(new URL(".", import.meta.url));
const mimeTypes = {
  ".html": "text/html; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".svg": "image/svg+xml",
  ".png": "image/png",
  ".woff2": "font/woff2"
};

/** Compile the component SCSS. No parameters; returns a Promise<void>. */
export async function build() {
  const result = sass.compile(resolve(projectRoot, "scss/style.scss"), {
    style: "expanded"
  });
  await mkdir(resolve(projectRoot, "css"), { recursive: true });
  await writeFile(resolve(projectRoot, "css/style.css"), result.css + "\n");
}

/**
 * Serve project assets on localhost.
 * @param {import("node:http").IncomingMessage} request - Incoming request.
 * @param {import("node:http").ServerResponse} response - HTTP response.
 * @returns {Promise<void>} Resolves after the response is sent.
 */
export async function serveRequest(request, response) {
  try {
    const url = new URL(request.url, "http://localhost");
    const path = decodeURIComponent(url.pathname);
    if (path === "/favicon.ico") {
      response.writeHead(204).end();
      return;
    }
    const target = resolve(projectRoot, "." + (path.endsWith("/") ? path + "index.html" : path));
    if (!target.startsWith(projectRoot.endsWith(sep) ? projectRoot : projectRoot + sep)) {
      response.writeHead(403).end("Forbidden");
      return;
    }
    const data = await readFile(target);
    response.writeHead(200, {
      "Content-Type": mimeTypes[extname(target)] || "application/octet-stream",
      "Cache-Control": "no-store"
    }).end(data);
  } catch {
    response.writeHead(404).end("Not found");
  }
}

/** Start localhost server and SCSS watch. No parameters; returns void. */
function serve() {
  createServer(serveRequest).listen(4174, "127.0.0.1", () => {
    console.info("SHOP-PC: http://127.0.0.1:4174");
  });
  gulp.watch("scss/**/*.scss", build);
}

export const dev = gulp.series(build, serve);
export default build;
