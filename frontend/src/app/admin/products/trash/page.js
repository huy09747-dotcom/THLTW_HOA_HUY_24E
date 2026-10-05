"use client";

import ManagementPage from "@/components/admin/ManagementPage";

export default function TrashPage() {
  return (
    <ManagementPage
      title="Thùng rác sản phẩm"
      endpoint="/products-trash"
      trash
      product
    />
  );
}
