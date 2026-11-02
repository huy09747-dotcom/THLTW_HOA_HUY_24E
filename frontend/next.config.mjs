/** @type {import('next').NextConfig} */
const nextConfig = {
  /* config options here */
  reactCompiler: false,
  allowedDevOrigins: ["127.0.0.1"],
  async rewrites() {
    const api = (process.env.LARAVEL_API_URL || "http://127.0.0.1:8000/api").replace(
      /\/$/,
      "",
    );
    return [{ source: "/api/laravel/:path*", destination: `${api}/:path*` }];
  },
};

export default nextConfig;
