import axiosClient from "@/lib/axiosClient";

export function getOrders(page, token) {
  return axiosClient.get("/donhangs", {
    params: { page },
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