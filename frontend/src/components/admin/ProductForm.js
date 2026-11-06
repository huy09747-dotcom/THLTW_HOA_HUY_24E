import { categoryOptions } from "@/utils/categories";
import EntityForm from "./EntityForm";

const fields = [
  { name: "name", label: "Tên sản phẩm", required: true, maxLength: 255 },
  { name: "slug", label: "Slug", required: true, maxLength: 255 },
  { name: "category_id", label: "Danh mục", type: "select", required: true },
  { name: "brand_id", label: "Thương hiệu", type: "select", required: true },
  {
    name: "price_buy",
    label: "Giá nhập",
    type: "money",
    min: 1000,
    required: true,
  },
  {
    name: "price_sale",
    label: "Giá bán",
    type: "money",
    min: 1000,
    required: true,
  },
  {
    name: "discount_percent",
    label: "Khuyến mãi (%)",
    type: "number",
    min: 0,
    max: 100,
    default: 0,
    required: true,
  },
  {
    name: "qty",
    label: "Số lượng tồn",
    type: "number",
    min: 0,
    required: true,
    default: 0,
  },
  { name: "image", label: "Hình ảnh", required: true, maxLength: 255 },
  {
    name: "unit",
    label: "Đơn vị",
    type: "select",
    valueType: "string",
    required: true,
    options: ["Cái", "Chiếc", "Bộ", "Hộp"].map((name) => ({ id: name, name })),
  },
  {
    name: "status",
    label: "Trạng thái",
    type: "select",
    default: 1,
    required: true,
    options: [
      { id: 1, name: "Hoạt động" },
      { id: 0, name: "Ẩn" },
    ],
  },
  { name: "is_available", label: "Cho phép đặt mua", type: "checkbox", default: 1 },
  { name: "is_featured", label: "Sản phẩm nổi bật", type: "checkbox", default: 0 },
  { name: "detail", label: "Chi tiết", type: "textarea", required: true },
  { name: "description", label: "Mô tả", type: "textarea", nullable: true },
];

export default function ProductForm(props) {
  return (
    <EntityForm
      {...props}
      fields={fields}
      options={{ ...props.options, category_id: categoryOptions(props.options?.category_id || []) }}
    />
  );
}
