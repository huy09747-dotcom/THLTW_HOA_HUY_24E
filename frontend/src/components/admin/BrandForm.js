import EntityForm from "./EntityForm";

const fields = [
  { name: "name", label: "Tên thương hiệu", required: true, maxLength: 255 },
  { name: "slug", label: "Slug", required: true, maxLength: 255 },
  { name: "image", label: "Đường dẫn hình ảnh", nullable: true, maxLength: 255 },
  { name: "title", label: "Tiêu đề", nullable: true, maxLength: 255 },
  {
    name: "sort_order",
    label: "Thứ tự",
    type: "number",
    min: 0,
    default: 0,
    nullable: true,
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
  { name: "description", label: "Mô tả", type: "textarea", required: true },
  { name: "content", label: "Nội dung", type: "textarea", required: true },
];

export default function BrandForm(props) {
  return (
    <EntityForm
      {...props}
      fields={fields}
    />
  );
}
