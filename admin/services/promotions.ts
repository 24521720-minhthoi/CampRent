import axiosInstance from "@/lib/axiosInstance";
import { PaginatedResponse } from "@/lib/response";
import { Promotion } from "@/lib/types";

export type PromotionPayload = {
  name: string;
  code?: string | null;
  type: "percent" | "fixed" | "bogo";
  value: number;
  scope: "all" | "category" | "product" | "shop";
  start_date?: string | null;
  end_date?: string | null;
  usage_limit?: number | null;
  per_user_limit?: number | null;
  min_order_value?: number;
  status?: "active" | "inactive" | "archived";
  is_active?: boolean;
  targets?: Array<{ target_type: "category" | "product" | "shop"; target_id: number }>;
};

export const getPromotions = async (): Promise<PaginatedResponse<Promotion>> => {
  const response = await axiosInstance.get(`${process.env.NEXT_PUBLIC_API_URL}/admin/promotions`);
  return response.data;
};

export const createPromotion = async (payload: PromotionPayload): Promise<Promotion> => {
  const response = await axiosInstance.post(`${process.env.NEXT_PUBLIC_API_URL}/admin/promotions`, payload);
  return response.data;
};

export const updatePromotion = async (id: number, payload: Partial<PromotionPayload>): Promise<Promotion> => {
  const response = await axiosInstance.put(`${process.env.NEXT_PUBLIC_API_URL}/admin/promotions/${id}`, payload);
  return response.data;
};

export const deletePromotion = async (id: number): Promise<{ message: string }> => {
  const response = await axiosInstance.delete(`${process.env.NEXT_PUBLIC_API_URL}/admin/promotions/${id}`);
  return response.data;
};
