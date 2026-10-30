const http = require("node:http");

const webhookPath = "/api/payments/payos/webhook";
const server = http.createServer(async (request, response) => {
  if (request.method !== "POST" || request.url !== webhookPath) {
    response.writeHead(404).end();
    return;
  }

  try {
    const chunks = [];
    let size = 0;
    for await (const chunk of request) {
      size += chunk.length;
      if (size > 65536) {
        response.writeHead(413).end();
        return;
      }
      chunks.push(chunk);
    }
    const result = await fetch(`http://127.0.0.1:8000${webhookPath}`, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: Buffer.concat(chunks),
      signal: AbortSignal.timeout(25000),
    });
    response.writeHead(result.status, { "Content-Type": "application/json" });
    response.end(JSON.stringify({ success: result.ok }));
  } catch {
    response.writeHead(502, { "Content-Type": "application/json" });
    response.end(JSON.stringify({ success: false }));
  }
});

server.requestTimeout = 30000;
server.listen(8010, "127.0.0.1", () => {
  console.log("payOS webhook proxy: http://127.0.0.1:8010");
});
