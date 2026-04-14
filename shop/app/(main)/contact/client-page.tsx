"use client";

import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";

export default function ClientContactPage() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [message, setMessage] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!name.trim() || !email.trim() || !message.trim()) {
      toast.error("Vui lòng điền đầy đủ thông tin.");
      return;
    }

    setLoading(true);
    try {
      await new Promise((r) => setTimeout(r, 700));
      setName("");
      setEmail("");
      setMessage("");
      toast.success("Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm.");
    } catch (err) {
      toast.error("Gửi thất bại. Vui lòng thử lại sau.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-[60vh] container mx-auto px-4 py-12">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div className="bg-card/80 p-6 rounded-lg shadow-sm">
          <h2 className="text-2xl font-semibold mb-2">Liên hệ với chúng tôi</h2>
          <p className="text-sm text-muted-foreground mb-6">
            Có câu hỏi về đồ cắm trại hoặc đơn thuê? Gửi thông tin và chúng tôi sẽ phản hồi sớm nhất.
          </p>

          <form onSubmit={submit} className="space-y-4">
            <div>
              <label className="text-sm mb-1 block">Họ và tên</label>
              <Input value={name} onChange={(e) => setName(e.target.value)} placeholder="Nguyễn Văn A" />
            </div>
            <div>
              <label className="text-sm mb-1 block">Email</label>
              <Input type="email" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="email@domain.com" />
            </div>
            <div>
              <label className="text-sm mb-1 block">Nội dung</label>
              <Textarea value={message} onChange={(e) => setMessage(e.target.value)} rows={6} placeholder="Mô tả ngắn về câu hỏi hoặc yêu cầu của bạn" />
            </div>
            <div className="flex items-center justify-end">
              <Button type="submit" disabled={loading}>{loading ? "Đang gửi..." : "Gửi liên hệ"}</Button>
            </div>
          </form>
        </div>

        <div className="space-y-6">
          <div className="p-6 bg-card/80 rounded-lg shadow-sm">
            <h3 className="text-lg font-medium">Thông tin liên hệ</h3>
            <p className="text-sm text-muted-foreground mt-2">Hotline: <strong>0901 234 567</strong></p>
            <p className="text-sm text-muted-foreground">Email: <strong>support@camprent.vn</strong></p>
            <p className="text-sm text-muted-foreground">Địa chỉ: <strong>Khu phố 6, P.Linh Trung, TP.Thủ Đức, TP.HCM</strong></p>
          </div>

          <div className="rounded-lg overflow-hidden border">
            <img
              src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800&q=60"
              alt="CampRent Outdoor"
              className="w-full h-64 object-cover"
            />
          </div>
        </div>
      </div>
    </div>
  );
}
