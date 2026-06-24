<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletesFlag;
use Database\Factories\BuyerFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Table('buyer', 'buyer_id', 'int')]
class Buyer extends Authenticatable
{
    use SoftDeletesFlag;
    /** @use HasFactory<BuyerFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'buyer_id';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'is_admin',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'is_deleted' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) ($this->is_admin ?? false);
    }

    public function isActive(): bool
    {
        return ! (bool) ($this->is_deleted ?? false);
    }

    // ── Relationships ─────────────────────────────────────────────────────

    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class, 'buyer_id', 'buyer_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'buyer_id', 'buyer_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'buyer_id', 'buyer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'buyer_id', 'buyer_id');
    }

    public function sellerApplications(): HasMany
    {
        return $this->hasMany(SellerApplication::class, 'buyer_id', 'buyer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id', 'buyer_id');
    }

    // ── Dashboard aggregations ─────────────────────────────────────────────

    /**
     * Base query builder for order_item joined through valid orders + products.
     */
    private function orderItemBaseQuery(): object
    {
        return DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $this->buyer_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded']);
    }

    public function totalSpent(): float
    {
        return (float) $this->orderItemBaseQuery()
            ->sum(DB::raw('product.price * order_item.quantity'));
    }

    public function totalOrdersPlaced(): int
    {
        return DB::table('order')
            ->where('buyer_id', $this->buyer_id)
            ->whereRaw('NOT is_deleted')
            ->count();
    }

    public function totalCancelledOrders(): int
    {
        return DB::table('order')
            ->where('buyer_id', $this->buyer_id)
            ->whereRaw('NOT is_deleted')
            ->where('status', 'Cancelled')
            ->count();
    }

    public function totalRefunds(): int
    {
        return DB::table('order')
            ->where('buyer_id', $this->buyer_id)
            ->whereRaw('NOT is_deleted')
            ->where('status', 'Refunded')
            ->count();
    }

    public function topCategories(int $limit = 7): array
    {
        return $this->orderItemBaseQuery()
            ->select('product.category', DB::raw('SUM(order_item.quantity) as total'))
            ->groupBy('product.category')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('total', 'category')
            ->toArray();
    }

    public function spendingOverTime(): array
    {
        return $this->orderItemBaseQuery()
            ->select(
                DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM') as month"),
                DB::raw('SUM(product.price * order_item.quantity) as total')
            )
            ->groupBy(DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    }

    public function spendByCategory(int $limit = 8): array
    {
        return $this->orderItemBaseQuery()
            ->select('product.category', DB::raw('SUM(product.price * order_item.quantity) as total'))
            ->groupBy('product.category')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('total', 'category')
            ->toArray();
    }

    public function purchaseFrequency(): array
    {
        return DB::table('order')
            ->where('buyer_id', $this->buyer_id)
            ->whereRaw('NOT is_deleted')
            ->select(
                DB::raw("to_char(ordered_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw("to_char(ordered_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    }

    public function topProducts(int $limit = 5): array
    {
        return $this->orderItemBaseQuery()
            ->select('product.name', DB::raw('SUM(order_item.quantity) as total'))
            ->groupBy('product.name')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('total', 'name')
            ->toArray();
    }

    public function reviewRatings(): array
    {
        $counts = DB::table('review')
            ->where('buyer_id', $this->buyer_id)
            ->select('rating', DB::raw('COUNT(*) as total'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->keyBy('rating');

        return collect(range(1, 5))
            ->mapWithKeys(fn($star) => [$star => (int) ($counts->get($star)?->total ?? 0)])
            ->toArray();
    }

    public function preferredSellers(int $limit = 7): array
    {
        return DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->join('seller', 'product.seller_id', '=', 'seller.seller_id')
            ->where('order.buyer_id', $this->buyer_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereRaw('NOT seller.is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select('seller.seller_name', DB::raw('SUM(product.price * order_item.quantity) as total'))
            ->groupBy('seller.seller_name')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('total', 'seller_name')
            ->toArray();
    }

    public function paymentMethods(): array
    {
        return DB::table('payment')
            ->join('order', 'payment.order_id', '=', 'order.order_id')
            ->where('order.buyer_id', $this->buyer_id)
            ->whereRaw('NOT "order".is_deleted')
            ->select('payment.payment_method', DB::raw('COUNT(*) as total'))
            ->groupBy('payment.payment_method')
            ->get()
            ->pluck('total', 'payment_method')
            ->toArray();
    }

    public function mostExpensiveItems(int $limit = 5): array
    {
        return DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $this->buyer_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereRaw('NOT product.is_deleted')
            ->select('product.product_id', 'product.name', 'product.price')
            ->distinct()
            ->orderBy('product.price', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'price' => (float) $r->price])
            ->toArray();
    }

    public function leastExpensiveItems(int $limit = 5): array
    {
        return DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('order.buyer_id', $this->buyer_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereRaw('NOT product.is_deleted')
            ->select('product.product_id', 'product.name', 'product.price')
            ->distinct()
            ->orderBy('product.price', 'asc')
            ->limit($limit)
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'price' => (float) $r->price])
            ->toArray();
    }
}
