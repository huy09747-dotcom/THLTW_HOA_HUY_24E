import EntityForm from "./EntityForm";

const fields = [
  { name: "name", label: "Tên danh mục", required: true, maxLength: 255 },
  { name: "slug", label: "Slug", required: true, maxLength: 255 },
  { name: "image", label: "Hình ảnh", nullable: true, maxLength: 255 },
  {
    name: "parent_id",
    label: "Danh mục cha",
    type: "select",
    min: 0,
    max: 4294967295,
    default: "",
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
  const categories = props.options?.parent_id || [];
  const excluded = new Set([Number(props.initial?.id)]);
  let changed = true;
  while (changed) {
    changed = false;
    for (const item of categories) {
      if (excluded.has(Number(item.parent_id)) && !excluded.has(Number(item.id))) {
        excluded.add(Number(item.id));
        changed = true;
      }
    }
  }
  const parents = categories
    .filter((item) => !excluded.has(Number(item.id)))
    .map((item) => {
      const names = [item.name];
      const visited = new Set([Number(item.id)]);
      let parentId = Number(item.parent_id);
      while (parentId && !visited.has(parentId)) {
        visited.add(parentId);
        const parent = categories.find((entry) => Number(entry.id) === parentId);
        if (!parent) {
          names.unshift("Cha không còn tồn tại");
          break;
        }
        names.unshift(parent.name);
        parentId = Number(parent.parent_id);
      }
      return { ...item, name: names.join(" → ") };
    });
  return (
    <EntityForm
      {...props}
      fields={fields}
      options={{
        ...props.options,
        parent_id: [{ id: 0, name: "Không có danh mục cha" }, ...parents],
      }}
    />
  );
}
