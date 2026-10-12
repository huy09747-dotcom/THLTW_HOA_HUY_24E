import axiosClient from "@/lib/axiosClient";

export function getUsers(page, token) {
  return axiosClient.get("/admin/users", {
    params: { page },
    token,
  });
}

export function getUserDetail(id, token) {
  return axiosClient.get(`/admin/users/${id}`, { token });
}

export function updateUserStatus(id, status, token) {
  return axiosClient.patch(`/admin/users/${id}/status`, { status }, { token });
}

export function getUserOrders(id, page, token) {
  return axiosClient.get(`/admin/users/${id}/orders`, {
    params: { page },
    token,
  });
}
