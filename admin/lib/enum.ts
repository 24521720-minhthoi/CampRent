export enum ProductStatus {
  IN_STOCK = "available",
  RENTING = "rented",
  MAINTENANCE = "maintenance",
  SUSPEND = "suspended",
  DISCONTINUE = "discontinued",
  OUT_OF_STOCK = "out_of_stock",
}

export enum Role {
  CUSTOMER = "customer",
  SHOP = "shop",
  ADMIN = "admin",
}

export enum PaymentMethod {
  CASH = "cash",
  BANK_TRANSFER = "bank_transfer",
  CARD = "card",
}

export enum OrderStatus {
  PENDING = "pending",
  CONFIRMED = "confirmed",
  PACKING = "packing",
  SHIPPING = "shipping",
  DELIVERED = "delivered",
  COMPLETED = "completed",
  CANCELLED = "cancelled",
  RETURNED = "returned",
  REFUNDED = "refunded",
}

export enum PaymentStatus {
  PENDING = "pending",
  COMPLETED = "completed",
  FAILED = "failed",
}

export enum UploadFolder {
  PRODUCT_IMAGES = "product-images",
  CATEGORY_IMAGES = "category-images",
}
