"use client";

import ManagementPage from "@/components/admin/ManagementPage";
import CategoryForm from "@/components/admin/CategoryForm";

export default function Page() {
  return (
    <ManagementPage
      title="Quản lý danh mục"
      endpoint="/categories"
      Form={CategoryForm}
      category
    />
  );
}
