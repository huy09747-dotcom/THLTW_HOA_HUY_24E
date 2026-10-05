"use client";

import ManagementPage from "@/components/admin/ManagementPage";
import ProductForm from "@/components/admin/ProductForm";

export default function Page() {
  return (
    <ManagementPage
      title="Quản lý sản phẩm"
      endpoint="/products"
      Form={ProductForm}
      product
    />
  );
}
