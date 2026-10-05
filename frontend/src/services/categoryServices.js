import { getAllPages } from "./api";

export function getCategories() {
  return getAllPages("/categories");
}
