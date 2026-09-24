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
  ".jpg": "image/jpeg",
  ".jpeg": "image/jpeg",
  ".woff2": "font/woff2"
};

/** Compile the component SCSS. */
export async function build() {
  const result = sass.compile(resolve(projectRoot, "scss/style.scss"), {
    style: "expanded"
  });
  await mkdir(resolve(projectRoot, "css"), { recursive: true });
  await writeFile(resolve(projectRoot, "css/style.css"), result.css + "\n");
}

/**
 * Serve project assets on localhost.
 * @param {import("node:http").IncomingMessage} request
 * @param {import("node:http").ServerResponse} response
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

/** Start localhost server and SCSS watch. */
function serve() {
  createServer(serveRequest).listen(4175, "127.0.0.1", () => {
    console.info("TRANSPORT: http://127.0.0.1:4175");
  });
  gulp.watch("scss/**/*.scss", build);
}

export const dev = gulp.series(build, serve);
export default build;
