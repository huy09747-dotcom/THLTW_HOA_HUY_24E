import CrudScreen from "@/components/admin/CrudScreen";
const fields = [
  { name: "name", label: "Tên chủ đề", required: true },
  { name: "slug", label: "Slug", required: true },
  { name: "sort_order", label: "Thứ tự", type: "number", default: 0, required: true },
  { name: "status", label: "Trạng thái", default: 1, options: [[1,"Hiển thị"],[0,"Ẩn"]] },
  { name: "description", label: "Mô tả", type: "textarea", max: 2000 },
];
const columns = [{name:"name",label:"Chủ đề"},{name:"slug",label:"Slug"},{name:"sort_order",label:"Thứ tự"},{name:"status",label:"Trạng thái"}];
export default function Page() { return <CrudScreen title="Chủ đề bài viết" endpoint="/admin/topics" fields={fields} columns={columns} />; }
