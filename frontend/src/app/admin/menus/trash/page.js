import ContentManagementPage from "@/components/admin/ContentManagementPage";

export const metadata = { title: "Thùng rác menu | HH Shop" };

export default function Page() {
  return <ContentManagementPage kind="menu" trash />;
}
