import CrudScreen from "@/components/admin/CrudScreen";
const fields = [
  {name:"name",label:"Người gửi",readOnly:true}, {name:"email",label:"Email",readOnly:true},
  {name:"phone",label:"Điện thoại",readOnly:true}, {name:"title",label:"Tiêu đề",readOnly:true},
  {name:"content",label:"Nội dung liên hệ",type:"textarea",readOnly:true},
  {name:"reply_content",label:"Nội dung phản hồi / ghi nhận xử lý",type:"textarea",max:10000},
  {name:"status",label:"Trạng thái xử lý",options:[[2,"Chưa xử lý"],[1,"Đã xử lý"]]},
];
const columns=[{name:"name",label:"Người gửi"},{name:"email",label:"Email"},{name:"title",label:"Tiêu đề"},{name:"status",label:"Trạng thái"},{name:"reply_at",label:"Lần xử lý gần nhất"}];
export default function Page() { return <CrudScreen title="Quản lý liên hệ" endpoint="/admin/contacts" fields={fields} columns={columns} allowCreate={false} statusOptions={[[2,"Chưa xử lý"],[1,"Đã xử lý"]]} />; }
