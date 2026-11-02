import PasswordResetForm from "@/components/shop/PasswordResetForm";

export const metadata = {
  title: "Đặt lại mật khẩu | HH Shop",
  robots: { index: false, follow: false },
  referrer: "no-referrer",
};

export default async function Page({ params, searchParams }) {
  const { token } = await params;
  const query = await searchParams;
  return <PasswordResetForm token={token} email={typeof query.email === "string" ? query.email : ""} />;
}
