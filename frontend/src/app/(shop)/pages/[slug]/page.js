import ContentPage from "@/components/shop/ContentPage";
export default async function Page({ params }) {
  const { slug } = await params;
  return <ContentPage slug={slug} title="Thông tin cửa hàng" />;
}
