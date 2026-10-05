"use client";

import ManagementPage from "@/components/admin/ManagementPage";

export default function TrashPage() {
  return (
    <ManagementPage
      title="Thùng rác danh mục"
      endpoint="/categories-trash"
      category
      trash
    />
  );
}
