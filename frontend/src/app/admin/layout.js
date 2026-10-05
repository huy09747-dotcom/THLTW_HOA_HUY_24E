import AdminAuthProvider from "@/components/admin/AdminAuthProvider";
import AdminLayout from "@/components/admin/AdminLayout";

export const metadata = {
  title: "Quản trị | HH Shop",
  robots: { index: false, follow: false },
};

export default function AdminRootLayout({ children }) {
  return (
    <main id="main-content">
      <AdminAuthProvider>
        <AdminLayout>{children}</AdminLayout>
      </AdminAuthProvider>
    </main>
  );
}
