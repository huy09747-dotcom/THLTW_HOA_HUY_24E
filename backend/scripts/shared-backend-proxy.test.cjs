const { test } = require("node:test");
const assert = require("node:assert/strict");
const http = require("node:http");
const { allowedPath, createProxy } = require("./shared-backend-proxy.cjs");

test("only API and read-only image paths are allowed", () => {
  assert.equal(allowedPath("/api/products?page=1", "GET"), true);
  assert.equal(allowedPath("/api/payments/payos/webhook", "POST"), true);
  assert.equal(allowedPath("/storage/images/photo.jpg", "GET"), true);
  for (const [path, method] of [["/.env", "GET"], ["/storage/logs/laravel.log", "GET"], ["/images/file.php", "GET"], ["/images/photo.jpg", "POST"], ["/api/%2e%2e%2f.env", "GET"], ["/api/products", "CONNECT"]]) {
    assert.equal(allowedPath(path, method), false);
  }
});

test("proxy forwards bearer tokens, preserves auth errors, limits body and hides server errors", async t => {
  const upstream = http.createServer((req, res) => {
    if (req.url === "/api/fail") { res.writeHead(500); res.end("private stack trace"); return; }
    if (req.url === "/api/user") { res.writeHead(401, { "Content-Type": "application/json" }); res.end('{"message":"Login required"}'); return; }
    res.setHeader("Content-Type", "application/json");
    res.end(JSON.stringify({ authorization: req.headers.authorization, path: req.url }));
  });
  await new Promise(resolve => upstream.listen(0, "127.0.0.1", resolve));
  const proxy = createProxy({ port: upstream.address().port, maxBytes: 20 });
  await new Promise(resolve => proxy.listen(0, "127.0.0.1", resolve));
  t.after(async () => {
    proxy.closeAllConnections(); upstream.closeAllConnections();
    await Promise.all([new Promise(resolve => proxy.close(resolve)), new Promise(resolve => upstream.close(resolve))]);
  });
  const base = `http://127.0.0.1:${proxy.address().port}`;
  const ok = await fetch(base + "/api/products?q=test", { headers: { Authorization: "Bearer test" } });
  assert.deepEqual(await ok.json(), { authorization: "Bearer test", path: "/api/products?q=test" });
  assert.equal((await fetch(base + "/.env")).status, 404);
  const unauthorized = await fetch(base + "/api/user");
  assert.equal(unauthorized.status, 401);
  const failed = await fetch(base + "/api/fail");
  assert.equal(failed.status, 502);
  assert.equal((await failed.text()).includes("stack trace"), false);
  assert.equal((await fetch(base + "/api/upload", { method: "POST", body: "x".repeat(21) })).status, 413);
});
