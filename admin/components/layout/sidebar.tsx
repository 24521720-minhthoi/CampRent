import {
  BadgePercent,
  FolderTree,
  LayoutDashboard,
  Package,
  ShoppingCart,
  Users,
} from "lucide-react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import UserMenu from "@/components/user-menu";

const navigation = [
  { name: "Dashboard", href: "/dashboard", icon: LayoutDashboard },
  { name: "Nguoi dung", href: "/users", icon: Users },
  { name: "Danh muc", href: "/categories", icon: FolderTree },
  { name: "San pham", href: "/products", icon: Package },
  { name: "Don hang", href: "/orders", icon: ShoppingCart },
  { name: "Khuyen mai", href: "/promotions", icon: BadgePercent },
];

export default function Sidebar() {
  return (
    <aside className="w-64 border-r bg-card flex flex-col">
      <div className="flex h-16 items-center border-b px-6">
        <h1 className="text-xl font-bold">CampRent Admin</h1>
      </div>
      <nav className="space-y-1 p-4 flex-1 overflow-auto">
        {navigation.map((item) => (
          <Link key={item.name} href={item.href}>
            <Button variant="ghost" className="w-full justify-start gap-3">
              <item.icon className="h-5 w-5" />
              {item.name}
            </Button>
          </Link>
        ))}
      </nav>
      <div className="border-t p-4">
        <UserMenu />
      </div>
    </aside>
  );
}
