import { Card, CardContent } from "@/components/ui/card";
import { Mail, Github } from "lucide-react";

const teamMembers = [
  {
    name: "Nguyễn Minh Thời",
    mssv: "24521720",
    role: "Nhóm trưởng",
    responsibility: "PM, Backend Integration, Documentation",
    email: "24521720@gm.uit.edu.vn",
  },
  {
    name: "Lưu Nhật Quang",
    mssv: "24521469",
    role: "Thành viên",
    responsibility: "AI Service, Backend Logic, API",
    email: "24521469@gm.uit.edu.vn",
  },
  {
    name: "Bùi Quốc Trung",
    mssv: "24521872",
    role: "Thành viên",
    responsibility: "Database, Infrastructure, DevOps",
    email: "24521872@gm.uit.edu.vn",
  },
  {
    name: "Chu Huỳnh Khánh An",
    mssv: "24520038",
    role: "Thành viên",
    responsibility: "Frontend, UI/UX, Branding",
    email: "24520038@gm.uit.edu.vn",
  },
];

export function TeamSection() {
  return (
    <section className="py-20">
      <div className="container mx-auto px-4">
        <div className="text-center space-y-4 mb-12">
          <h2 className="text-3xl font-bold">Đội ngũ phát triển</h2>
          <p className="text-muted-foreground max-w-2xl mx-auto">
            Lớp IS207.Q22 — GVHD: ThS. Vũ Minh Sang
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {teamMembers.map((member) => (
            <Card key={member.mssv} className="text-center rounded-2xl">
              <CardContent className="p-6 space-y-3">
                <div className="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto text-2xl font-bold text-primary">
                  {member.name.charAt(member.name.lastIndexOf(" ") + 1)}
                </div>
                <div>
                  <h3 className="font-semibold text-lg">{member.name}</h3>
                  <p className="text-sm text-muted-foreground">{member.mssv}</p>
                  <p className="text-sm font-medium text-primary">{member.role}</p>
                  <p className="text-xs text-muted-foreground mt-1">{member.responsibility}</p>
                </div>
                <div className="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                  <Mail className="h-3 w-3" />
                  <span className="text-xs">{member.email}</span>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
}
