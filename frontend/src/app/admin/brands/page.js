"use client";

import ManagementPage from "@/components/admin/ManagementPage";
import BrandForm from "@/components/admin/BrandForm";

export default function Page() {
  return (
    <ManagementPage
      title="Quản lý thương hiệu"
      endpoint="/brands"
      Form={BrandForm}
    />
  );
}
