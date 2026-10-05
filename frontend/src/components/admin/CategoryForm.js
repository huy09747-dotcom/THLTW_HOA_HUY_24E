import EntityForm from "./EntityForm";

const fields = [
  { name: "name", label: "Tên danh mục", required: true, maxLength: 255 },
  { name: "slug", label: "Slug", required: true, maxLength: 255 },
  { name: "image", label: "Đường dẫn hình ảnh", nullable: true, maxLength: 255 },
  {
    name: "parent_id",
    label: "ID danh mục cha (0 nếu không có)",
    type: "number",
    min: 0,
    max: 4294967295,
    default: 0,
    required: true,
  },
  {
    name: "sort_order",
    label: "Thứ tự",
    type: "number",
    min: 0,
    max: 4294967295,
    default: 0,
    required: true,
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
  { name: "description", label: "Mô tả", type: "textarea", nullable: true },
];

export default function CategoryForm(props) {
  return (
    <EntityForm
      {...props}
      fields={fields}
    />
  );
}
