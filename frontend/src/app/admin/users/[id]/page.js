import UserDetail from "@/components/admin/UserDetail";

export const metadata = { title: "Chi tiết người dùng | HH Shop" };

export default async function UserDetailPage({ params }) {
  const { id } = await params;

  return <UserDetail key={id} id={id} />;
}
