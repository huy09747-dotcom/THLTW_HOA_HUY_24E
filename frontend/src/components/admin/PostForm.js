import EntityForm from "./EntityForm";

const fields = [
  { name: "title", label: "Tiêu đề", required: true, maxLength: 255 },
  { name: "slug", label: "Slug", required: true, maxLength: 255 },
  { name: "topic_id", label: "Chủ đề", type: "select", nullable: true },
  { name: "image", label: "Hình ảnh", nullable: true },
  {
    name: "status",
    label: "Trạng thái",
    type: "select",
    default: 1,
    required: true,
    options: [
      { id: 1, name: "Hoạt động" },
      { id: 0, name: "Ẩn" },
      { id: 2, name: "Chưa xuất bản" },
    ],
  },
  { name: "description", label: "Mô tả", type: "textarea", nullable: true },
  { name: "detail", label: "Nội dung", type: "textarea", required: true },
];

export default function PostForm({ options, ...props }) {
  return (
    <EntityForm
      {...props}
      fields={fields}
      slugSource="title"
      options={{ topic_id: [{ id: "", name: "Không chọn chủ đề" }, ...(options.topics || [])] }}
    />
  );
}
