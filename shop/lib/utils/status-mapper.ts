import { ProductStatus } from "@/lib/enum";

export const getProductStatusLabel = (status: string | ProductStatus): string => {
  switch (status) {
    case ProductStatus.IN_STOCK:
    case "available":
      return "Còn hàng";
    case ProductStatus.RENTING:
    case "rented":
      return "Đang cho thuê";
    case ProductStatus.MAINTENANCE:
    case "maintenance":
      return "Bảo trì";
    case ProductStatus.SUSPEND:
    case "suspended":
      return "Tạm ngưng";
    case ProductStatus.DISCONTINUE:
    case "discontinued":
      return "Ngừng kinh doanh";
    case ProductStatus.OUT_OF_STOCK:
    case "out_of_stock":
      return "Hết hàng";
    default:
      return "Không xác định";
  }
};
