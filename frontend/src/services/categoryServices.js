import { getAllPages } from "./api";
import { USE_DEMO_CATALOG, demoCategories } from "@/data/demoCatalog";

export function getCategories() {
  if (USE_DEMO_CATALOG) {
    return Promise.resolve(demoCategories);
  }

  return getAllPages("/categories");
}
