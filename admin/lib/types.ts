import {
  OrderStatus,
  PaymentMethod,
  PaymentStatus,
  ProductStatus,
  Role,
} from "./enum";

export interface User {
  id: number;
  name: string;
  email: string;
  role: Role;
  address: string | null;
  avatar_url: string | null;
  created_at: string;
}

export interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  image_url?: string;
  created_at: string;
}

export interface Product {
  id: number;
  name: string;
  slug: string;
  description: string;
  price: number;
  deposit_amount: number;
  pricing?: ProductPricing;
  stock: number;
  image_url?: string;
  images?: string[];
  status: ProductStatus;
  category_id: number;
  category?: Category;
  shop_id: number;
  shop?: User;
  created_at: string;
  updated_at: string;
  latest_price_change?: ProductPriceHistory;
}

export interface ProductPricing {
  base_price: number;
  final_price: number;
  discount_amount: number;
  discount_percent: number;
  sale_badge: boolean;
  promotion: {
    id: number;
    name: string;
    type: string;
    value: number;
  } | null;
}

export interface PromotionTarget {
  id: number;
  promotion_id: number;
  target_type: "category" | "product" | "shop";
  target_id: number;
}

export interface Promotion {
  id: number;
  name: string;
  code: string | null;
  type: "percent" | "fixed" | "bogo";
  value: string;
  scope: "all" | "category" | "product" | "shop";
  start_date: string | null;
  end_date: string | null;
  usage_limit: number | null;
  per_user_limit: number | null;
  min_order_value: string;
  status: "active" | "inactive" | "archived";
  is_active: boolean;
  targets: PromotionTarget[];
  created_at: string;
  updated_at: string;
}

export interface ProductPriceHistory {
  id: number;
  product_id: number;
  old_price: string;
  new_price: string;
  changed_by: number | null;
  changed_by_user?: User;
  reason: string | null;
  effective_date: string;
  created_at: string;
  updated_at: string;
}

export interface CartItem {
  id: number;
  product: Product;
  quantity: number;
  start_date: string;
  end_date: string;
  days: number;
  total_price: number;
  created_at: string;
  updated_at: string;
}

export interface PricingSummary {
  rental_subtotal: number;
  deposit_total: number;
  insurance_fee: number;
  shipping_fee: number;
  discount_total: number;
  total_amount: number;
  discounts: Array<{
    promotion_id: number;
    code: string | null;
    name: string;
    type: string;
    value: number;
    amount: number;
    level: string;
  }>;
}

export interface Order {
  id: number;
  user_id: number;
  user: User;
  start_date: string;
  end_date: string;
  rental_subtotal: string;
  deposit_total: string;
  insurance_fee: string;
  shipping_fee: string;
  discount_total: string;
  total_amount: string;
  pricing_snapshot?: PricingSummary;
  status: OrderStatus;
  address: string | null;
  created_at: string;
  updated_at: string;
  items: OrderItem[];
  payment: Payment;
  status_histories?: OrderStatusHistory[];
}

export interface OrderStatusHistory {
  id: number;
  order_id: number;
  old_status: OrderStatus | null;
  new_status: OrderStatus;
  changed_by: number | null;
  actor_role: string | null;
  reason: string | null;
  created_at: string;
  actor?: User | null;
}

export interface OrderItem {
  id: number;
  order_id: number;
  product_id: number;
  product: Product;
  quantity: number;
  price: string;
  unit_deposit: string;
  days: number;
  rental_subtotal: string;
  discount_amount: string;
  deposit_total: string;
  subtotal: string;
  total_amount: string;
  start_date: string;
  end_date: string;
  created_at: string;
  updated_at: string;
}

export interface Payment {
  id: number;
  order_id: number;
  payment_method: PaymentMethod;
  amount: string;
  status: PaymentStatus;
  created_at: string;
  updated_at: string;
}

export interface Comment {
  id: number;
  content: string;
  user_id: number;
  product_id: number;
  left: number;
  right: number;
  parent_id: number | null;
  edited: boolean;
  edited_at: string | null;
  created_at: string;
  updated_at: string;
  user: User;
  parent: Comment | null;
}

export type ProductFilters = {
  min_price?: number;
  max_price?: number;
  categories?: number[];
  status?: string[];
  q?: string;
  sort?: string;
};
