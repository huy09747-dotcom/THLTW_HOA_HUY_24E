const http = require("node:http");

function allowedPath(rawUrl, method) {
  try {
    const url = new URL(rawUrl, "http://localhost");
    const path = decodeURIComponent(url.pathname);
    if (path.includes("\\") || path.split("/").includes("..")) return false;
    if (path.startsWith("/api/")) {
      return ["GET", "HEAD", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"].includes(method);
    }
    return ["GET", "HEAD"].includes(method)
      && (path.startsWith("/images/") || path.startsWith("/storage/images/"))
      && /\.(png|jpe?g|webp|gif|svg|ico)$/i.test(path);
  } catch {
    return false;
  }
}

function createProxy({ port = 8000, maxBytes = 6 * 1024 * 1024 } = {}) {
  return http.createServer((request, response) => {
    response.setHeader("X-Content-Type-Options", "nosniff");
    if (!allowedPath(request.url, request.method)) {
      response.writeHead(404).end();
      request.resume();
      return;
    }
    const chunks = [];
    let size = 0;
    let exceeded = false;
    request.on("data", chunk => {
      size += chunk.length;
      if (size > maxBytes) {
        exceeded = true;
        chunks.length = 0;
        if (!response.headersSent) response.writeHead(413).end();
        return;
      }
      if (!exceeded) chunks.push(chunk);
    });
    request.on("error", () => response.destroy());
    request.on("end", () => {
      if (exceeded) return;
      const headers = {};
      for (const name of ["accept", "content-type", "authorization", "user-agent", "x-requested-with", "x-guest-token"]) {
        if (request.headers[name]) headers[name] = request.headers[name];
      }
      headers["content-length"] = String(size);
      const upstream = http.request({
        hostname: "127.0.0.1",
        port,
        path: request.url,
        method: request.method,
        headers,
      }, result => {
        const outputHeaders = {};
        for (const name of ["content-type", "cache-control", "etag", "last-modified", "retry-after"]) {
          if (result.headers[name]) outputHeaders[name] = result.headers[name];
        }
        if (result.statusCode >= 500) {
          result.resume();
          response.writeHead(502, { "Content-Type": "application/json" });
          response.end(JSON.stringify({ message: "Máy chủ tạm thời không phản hồi. Vui lòng thử lại." }));
          return;
        }
        response.writeHead(result.statusCode, outputHeaders);
        result.pipe(response);
        result.on("error", () => response.destroy());
      });
      upstream.setTimeout(45000, () => upstream.destroy());
      upstream.on("error", () => {
        if (!response.headersSent) {
          response.writeHead(502, { "Content-Type": "application/json" });
          response.end(JSON.stringify({ message: "Chưa kết nối được backend." }));
        } else response.destroy();
      });
      response.on("close", () => upstream.destroy());
      upstream.end(Buffer.concat(chunks));
    });
  });
}

if (require.main === module) {
  const server = createProxy();
  server.requestTimeout = 60000;
  server.listen(8011, "127.0.0.1", () => {
    console.log("Shared backend proxy: http://127.0.0.1:8011 -> Laravel :8000 (API and images only)");
  });
}

module.exports = { allowedPath, createProxy };
