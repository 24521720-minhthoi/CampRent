"use client";

import { useMemo, useState } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Switch } from "@/components/ui/switch";
import {
  createPromotion,
  deletePromotion,
  getPromotions,
  updatePromotion,
  type PromotionPayload,
} from "@/services/promotions";
import type { Promotion } from "@/lib/types";
import { formatCurrency, formatDate } from "@/lib/utils";

const defaultForm: PromotionPayload = {
  name: "",
  code: "",
  type: "percent",
  value: 10,
  scope: "all",
  start_date: "",
  end_date: "",
  usage_limit: null,
  per_user_limit: null,
  min_order_value: 0,
  status: "active",
  is_active: true,
  targets: [],
};

export default function PromotionsPage() {
  const queryClient = useQueryClient();
  const [editing, setEditing] = useState<Promotion | null>(null);
  const [form, setForm] = useState<PromotionPayload>(defaultForm);

  const { data, isLoading, isError } = useQuery({
    queryKey: ["promotions"],
    queryFn: getPromotions,
  });

  const promotions = data?.data ?? [];

  const saveMutation = useMutation({
    mutationFn: () => {
      const payload = normalizePayload(form);
      return editing
        ? updatePromotion(editing.id, payload)
        : createPromotion(payload);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["promotions"] });
      setEditing(null);
      setForm(defaultForm);
    },
  });

  const deleteMutation = useMutation({
    mutationFn: deletePromotion,
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ["promotions"] }),
  });

  const activeCount = useMemo(
    () => promotions.filter((promotion) => promotion.is_active && promotion.status === "active").length,
    [promotions]
  );

  const startEdit = (promotion: Promotion) => {
    setEditing(promotion);
    setForm({
      name: promotion.name,
      code: promotion.code ?? "",
      type: promotion.type,
      value: Number(promotion.value),
      scope: promotion.scope,
      start_date: toDateTimeLocal(promotion.start_date),
      end_date: toDateTimeLocal(promotion.end_date),
      usage_limit: promotion.usage_limit,
      per_user_limit: promotion.per_user_limit,
      min_order_value: Number(promotion.min_order_value),
      status: promotion.status,
      is_active: promotion.is_active,
      targets: promotion.targets?.map((target) => ({
        target_type: target.target_type,
        target_id: target.target_id,
      })) ?? [],
    });
  };

  const toggleActive = (promotion: Promotion) => {
    updatePromotion(promotion.id, {
      is_active: !promotion.is_active,
      status: promotion.status,
    }).then(() => queryClient.invalidateQueries({ queryKey: ["promotions"] }));
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Quan ly khuyen mai</h1>
        <p className="text-muted-foreground">
          Tao voucher, flash sale, giam gia san pham va BOGO cho CampRent.
        </p>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        <Card>
          <CardHeader>
            <CardTitle className="text-sm font-medium">Tong khuyen mai</CardTitle>
          </CardHeader>
          <CardContent className="text-2xl font-bold">{promotions.length}</CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle className="text-sm font-medium">Dang hoat dong</CardTitle>
          </CardHeader>
          <CardContent className="text-2xl font-bold">{activeCount}</CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle className="text-sm font-medium">Voucher demo</CardTitle>
          </CardHeader>
          <CardContent className="text-2xl font-bold">CAMP50K</CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>{editing ? "Cap nhat khuyen mai" : "Them khuyen mai"}</CardTitle>
        </CardHeader>
        <CardContent>
          <form
            className="grid gap-4 md:grid-cols-4"
            onSubmit={(event) => {
              event.preventDefault();
              saveMutation.mutate();
            }}
          >
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="name">Ten khuyen mai</Label>
              <Input
                id="name"
                value={form.name}
                onChange={(event) => setForm({ ...form, name: event.target.value })}
                required
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="code">Ma voucher</Label>
              <Input
                id="code"
                value={form.code ?? ""}
                onChange={(event) => setForm({ ...form, code: event.target.value.toUpperCase() })}
                placeholder="De trong neu sale tu dong"
              />
            </div>
            <div className="space-y-2">
              <Label>Loai</Label>
              <Select value={form.type} onValueChange={(value: PromotionPayload["type"]) => setForm({ ...form, type: value })}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="percent">Phan tram</SelectItem>
                  <SelectItem value="fixed">So tien co dinh</SelectItem>
                  <SelectItem value="bogo">Mua 1 tang 1</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="value">Gia tri</Label>
              <Input
                id="value"
                type="number"
                min={0}
                value={form.value}
                onChange={(event) => setForm({ ...form, value: Number(event.target.value) })}
              />
            </div>
            <div className="space-y-2">
              <Label>Pham vi</Label>
              <Select value={form.scope} onValueChange={(value: PromotionPayload["scope"]) => setForm({ ...form, scope: value, targets: [] })}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Toan san</SelectItem>
                  <SelectItem value="category">Danh muc</SelectItem>
                  <SelectItem value="product">San pham</SelectItem>
                  <SelectItem value="shop">Cua hang</SelectItem>
                </SelectContent>
              </Select>
            </div>
            {form.scope !== "all" && (
              <div className="space-y-2">
                <Label htmlFor="target">ID ap dung</Label>
                <Input
                  id="target"
                  type="number"
                  min={1}
                  value={form.targets?.[0]?.target_id ?? ""}
                  onChange={(event) =>
                    setForm({
                      ...form,
                      targets: event.target.value
                        ? [{ target_type: form.scope as "category" | "product" | "shop", target_id: Number(event.target.value) }]
                        : [],
                    })
                  }
                />
              </div>
            )}
            <div className="space-y-2">
              <Label htmlFor="min">Don toi thieu</Label>
              <Input
                id="min"
                type="number"
                min={0}
                value={form.min_order_value ?? 0}
                onChange={(event) => setForm({ ...form, min_order_value: Number(event.target.value) })}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="limit">Gioi han luot</Label>
              <Input
                id="limit"
                type="number"
                min={1}
                value={form.usage_limit ?? ""}
                onChange={(event) => setForm({ ...form, usage_limit: event.target.value ? Number(event.target.value) : null })}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="start">Bat dau</Label>
              <Input
                id="start"
                type="datetime-local"
                value={form.start_date ?? ""}
                onChange={(event) => setForm({ ...form, start_date: event.target.value })}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="end">Ket thuc</Label>
              <Input
                id="end"
                type="datetime-local"
                value={form.end_date ?? ""}
                onChange={(event) => setForm({ ...form, end_date: event.target.value })}
              />
            </div>
            <div className="flex items-end gap-2">
              <Switch
                checked={form.is_active}
                onCheckedChange={(checked) => setForm({ ...form, is_active: checked })}
              />
              <Label>Dang bat</Label>
            </div>
            <div className="flex items-end gap-2 md:col-span-4">
              <Button type="submit" disabled={saveMutation.isPending}>
                {editing ? "Luu thay doi" : "Tao khuyen mai"}
              </Button>
              {editing && (
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => {
                    setEditing(null);
                    setForm(defaultForm);
                  }}
                >
                  Huy
                </Button>
              )}
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Danh sach khuyen mai</CardTitle>
        </CardHeader>
        <CardContent className="space-y-3">
          {isLoading && <p>Dang tai khuyen mai...</p>}
          {isError && <p className="text-destructive">Khong tai duoc khuyen mai.</p>}
          {promotions.map((promotion) => (
            <div key={promotion.id} className="grid gap-3 rounded-lg border p-4 md:grid-cols-[1fr_auto]">
              <div className="space-y-2">
                <div className="flex flex-wrap items-center gap-2">
                  <h3 className="font-semibold">{promotion.name}</h3>
                  {promotion.code && <Badge>{promotion.code}</Badge>}
                  <Badge variant={promotion.is_active ? "default" : "secondary"}>
                    {promotion.is_active ? "Active" : "Off"}
                  </Badge>
                  <Badge variant="outline">{promotion.type}</Badge>
                  <Badge variant="outline">{promotion.scope}</Badge>
                </div>
                <div className="text-sm text-muted-foreground">
                  Gia tri: {promotion.type === "percent" ? `${promotion.value}%` : formatCurrency(promotion.value)} | Don toi thieu: {formatCurrency(promotion.min_order_value)}
                </div>
                <div className="text-sm text-muted-foreground">
                  Thoi gian: {promotion.start_date ? formatDate(promotion.start_date) : "Khong gioi han"} - {promotion.end_date ? formatDate(promotion.end_date) : "Khong gioi han"}
                </div>
              </div>
              <div className="flex items-center gap-2">
                <Switch checked={promotion.is_active} onCheckedChange={() => toggleActive(promotion)} />
                <Button variant="outline" onClick={() => startEdit(promotion)}>Sua</Button>
                <Button variant="destructive" onClick={() => deleteMutation.mutate(promotion.id)}>Xoa</Button>
              </div>
            </div>
          ))}
        </CardContent>
      </Card>
    </div>
  );
}

function normalizePayload(form: PromotionPayload): PromotionPayload {
  return {
    ...form,
    code: form.code?.trim() ? form.code.trim().toUpperCase() : null,
    start_date: form.start_date || null,
    end_date: form.end_date || null,
    usage_limit: form.usage_limit || null,
    per_user_limit: form.per_user_limit || null,
    min_order_value: form.min_order_value || 0,
    targets: form.scope === "all" ? [] : form.targets,
  };
}

function toDateTimeLocal(value: string | null): string {
  if (!value) return "";
  return new Date(value).toISOString().slice(0, 16);
}
