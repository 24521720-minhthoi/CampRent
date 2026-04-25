<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::with('targets')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->boolean('active_only')) {
            $query->currentlyActive();
        }

        return response()->json($query->paginate((int) $request->query('per_page', 20)));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedPayload($request);
        $targets = $validated['targets'] ?? [];
        unset($validated['targets']);

        $promotion = Promotion::create($this->normalize($validated));
        $this->syncTargets($promotion, $targets);

        return response()->json($promotion->fresh('targets'), 201);
    }

    public function show(Promotion $promotion)
    {
        return response()->json($promotion->load('targets'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $validated = $this->validatedPayload($request, true);
        $targets = $validated['targets'] ?? null;
        unset($validated['targets']);

        $promotion->update($this->normalize($validated));

        if (is_array($targets)) {
            $this->syncTargets($promotion, $targets);
        }

        return response()->json($promotion->fresh('targets'));
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return response()->json(['message' => 'Promotion deleted']);
    }

    private function validatedPayload(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64', Rule::unique('promotions', 'code')->ignore($request->route('promotion'))],
            'type' => [$required, Rule::in(['percent', 'fixed', 'bogo'])],
            'value' => [$required, 'numeric', 'min:0'],
            'scope' => [$required, Rule::in(['all', 'category', 'product', 'shop'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'new_user_only' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'archived'])],
            'is_active' => ['nullable', 'boolean'],
            'targets' => ['nullable', 'array'],
            'targets.*.target_type' => ['required_with:targets', Rule::in(['category', 'product', 'shop'])],
            'targets.*.target_id' => ['required_with:targets', 'integer', 'min:1'],
        ]);
    }

    private function normalize(array $payload): array
    {
        if (array_key_exists('code', $payload) && $payload['code']) {
            $payload['code'] = strtoupper(trim($payload['code']));
        }

        $payload['min_order_value'] = $payload['min_order_value'] ?? 0;
        $payload['new_user_only'] = $payload['new_user_only'] ?? false;
        $payload['status'] = $payload['status'] ?? 'active';
        $payload['is_active'] = $payload['is_active'] ?? true;

        return $payload;
    }

    private function syncTargets(Promotion $promotion, array $targets): void
    {
        $promotion->targets()->delete();

        if ($promotion->scope === 'all') {
            return;
        }

        foreach ($targets as $target) {
            $promotion->targets()->create([
                'target_type' => $target['target_type'],
                'target_id' => $target['target_id'],
            ]);
        }
    }
}
