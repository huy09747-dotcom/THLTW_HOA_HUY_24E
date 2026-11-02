import axiosClient from "@/lib/axiosClient";

export function getOrders(page, token, filters = "") {
  return axiosClient.get("/donhangs", {
    params: { page, ...Object.fromEntries(new URLSearchParams(filters)) },
    token,
  });
}

export function getOrderDetail(id, token) {
  return axiosClient.get(`/donhangs/${id}`, {
    token,
  });
}

export function updateOrderStatus(id, status, token) {
  return axiosClient.patch(
    `/donhangs/${id}/status`,
    { status },
    { token }
  );
}