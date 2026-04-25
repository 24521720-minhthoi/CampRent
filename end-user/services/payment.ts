import axiosInstance from "@/lib/axiosInstance";
import { CheckoutByCardResponse } from "@/lib/response";

export const checkoutByCard = async ({
  address,
  promotionCode,
}: {
  address: string;
  promotionCode?: string;
}): Promise<CheckoutByCardResponse> => {
  const response = await axiosInstance.post("/checkout/card", {
    address,
    promotion_code: promotionCode || undefined,
  });
  return response.data;
};

export const checkoutByCash = async ({
  address,
  promotionCode,
}: {
  address: string;
  promotionCode?: string;
}): Promise<{ message: string; order_id: string }> => {
  const response = await axiosInstance.post("/checkout/cash", {
    address,
    promotion_code: promotionCode || undefined,
  });
  return response.data;
};
