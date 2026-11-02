import CrudScreen from "@/components/admin/CrudScreen";
const fields = [
  { name: "title", label: "Tiêu đề", required: true },
  { name: "slug", label: "Đường dẫn (slug)", required: true },
  { name: "status", label: "Trạng thái", default: 1, options: [[1,"Hiển thị"],[0,"Ẩn"],[2,"Bản nháp"]] },
  { name: "description", label: "Mô tả", type: "textarea", max: 2000 },
  { name: "detail", label: "Nội dung", type: "textarea", rows: 12, required: true },
];
const columns = [{name:"title",label:"Tiêu đề"},{name:"slug",label:"Đường dẫn"},{name:"status",label:"Trạng thái"}];
export default function Page() { return <CrudScreen title="Quản lý trang đơn" endpoint="/admin/pages" fields={fields} columns={columns} statusOptions={[[1,"Hiển thị"],[0,"Ẩn"],[2,"Bản nháp"]]} pageLinks />; }
