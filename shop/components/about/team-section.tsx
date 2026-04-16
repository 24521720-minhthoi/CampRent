import { Card, CardContent } from "@/components/ui/card";
import { Mail } from "lucide-react";

const teamMembers = [
  { 
    name: "Nguyễn Minh Thời", 
    mssv: "24521720", 
    role: "Nhóm trưởng", 
    responsibility: "PM, Backend, Docs", 
    email: "24521720@gm.uit.edu.vn",
    avatar: "/thoi.png"
  },
  { 
    name: "Lưu Nhật Quang", 
    mssv: "24521469", 
    role: "Thành viên", 
    responsibility: "AI Service, Backend Logic", 
    email: "24521469@gm.uit.edu.vn",
    avatar: "/quang.png"
  },
  { 
    name: "Bùi Quốc Trung", 
    mssv: "24521872", 
    role: "Thành viên", 
    responsibility: "Database, Infrastructure", 
    email: "24521872@gm.uit.edu.vn",
    avatar: "/trung.png"
  },
  { 
    name: "Chu Huỳnh Khánh An", 
    mssv: "24520038", 
    role: "Thành viên", 
    responsibility: "Frontend, UI/UX, Branding", 
    email: "24520038@gm.uit.edu.vn",
    avatar: "/an.png"
  },
];

export function TeamSection() {
  return (
    <section className="py-20">
      <div className="container mx-auto px-4">
        <div className="text-center space-y-4 mb-12">
          <h2 className="text-3xl font-bold">Đội ngũ phát triển</h2>
          <p className="text-muted-foreground max-w-2xl mx-auto">Lớp IS207.Q22 — GVHD: ThS. Vũ Minh Sang</p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {teamMembers.map((member) => (
            <Card key={member.mssv} className="text-center rounded-2xl">
              <CardContent className="p-6 space-y-3">
                <div className="w-24 h-24 mx-auto mb-4 overflow-hidden rounded-full border-4 border-primary/20">
                  <img 
                    src={member.avatar} 
                    alt={member.name} 
                    className="w-full h-full object-cover"
                  />
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
