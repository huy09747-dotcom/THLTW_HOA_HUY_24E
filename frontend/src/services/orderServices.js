import axiosClient from "@/lib/axiosClient";

export function getOrders(page, token) {
  return axiosClient.get("/donhangs", {
    params: { page },
    token,
  });
}
