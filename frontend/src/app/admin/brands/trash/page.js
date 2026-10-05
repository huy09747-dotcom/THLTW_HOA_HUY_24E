"use client";

import ManagementPage from "@/components/admin/ManagementPage";

export default function TrashPage() {
  return (
    <ManagementPage
      title="Thùng rác thương hiệu"
      endpoint="/brands-trash"
      trash
      
    />
  );
}
