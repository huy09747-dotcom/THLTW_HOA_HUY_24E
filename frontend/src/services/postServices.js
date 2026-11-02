import axiosClient from "@/lib/axiosClient";

const path = "/admin/posts";

export function getList(page, search, trash, token, status = "") {
  return axiosClient.get(trash ? `${path}/trash` : path, {
    params: { page, search: search || undefined, status: status || undefined },
    token,
  });
}

export function getOptions(token) {
  return axiosClient.get(`${path}/options`, { token });
}

export function saveItem(id, body, token) {
  return id
    ? axiosClient.put(`${path}/${id}`, body, { token })
    : axiosClient.post(path, body, { token });
}

export function removeItem(id, permanent, token) {
  return axiosClient.delete(`${path}/${id}${permanent ? "/force" : ""}`, { token });
}

export function restoreItem(id, token) {
  return axiosClient.patch(`${path}/${id}/restore`, {}, { token });
}
