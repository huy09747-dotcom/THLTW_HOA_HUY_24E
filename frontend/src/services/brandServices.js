import axiosClient from "@/lib/axiosClient";

// Trả về { data, links, meta } để hiển thị danh sách và phân trang.
export function getBrands(params = {}) {
  return axiosClient.get("/brands", { params });
}

export function createBrand(data, token) {
  return axiosClient.post("/brands", data, { token });
}

export function updateBrand(id, data, token) {
  return axiosClient.put(`/brands/${encodeURIComponent(id)}`, data, { token });
}

// Xóa mềm: chuyển thương hiệu vào thùng rác.
export function deleteBrand(id, token) {
  return axiosClient.delete(`/brands/${encodeURIComponent(id)}`, { token });
}

export function getTrashBrands(params = {}, token) {
  return axiosClient.get("/brands-trash", { params, token });
}

export function restoreBrand(id, token) {
  return axiosClient.patch(
    `/brands/${encodeURIComponent(id)}/restore`,
    {},
    { token },
  );
}

export function forceDeleteBrand(id, token) {
  return axiosClient.delete(`/brands/${encodeURIComponent(id)}/force`, { token });
}