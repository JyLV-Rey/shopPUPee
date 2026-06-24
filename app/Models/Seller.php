<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletesFlag;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Table('seller', 'seller_id', 'int')]
class Seller extends Model
{
    use SoftDeletesFlag;

    public $timestamps = false;

    protected $fillable = [
        'buyer_id',
        'seller_name',
        'address_id',
        'application_id',
    ];

    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'buyer_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id', 'seller_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(SellerApplication::class, 'application_id', 'application_id');
    }

    // ── Dashboard aggregations (aggregate SQL)

    /**
     * Base query for this seller's order_items through valid orders.
     */
    private function soldItemBaseQuery(): object
    {
        return DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('product.seller_id', $this->seller_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded']);
    }

    public function totalProductsListed(): int
    {
        return (int) Product::where('seller_id', $this->seller_id)->count();
    }

    public function totalItemsSold(): int
    {
        return (int) $this->soldItemBaseQuery()
            ->sum('order_item.quantity');
    }

    public function totalRevenue(): float
    {
        return (float) $this->soldItemBaseQuery()
            ->sum(DB::raw('product.price * order_item.quantity'));
    }

    public function totalCancelled(): int
    {
        return DB::table('order')
            ->join('order_item', 'order.order_id', '=', 'order_item.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('product.seller_id', $this->seller_id)
            ->whereRaw('NOT "order".is_deleted')
            ->where('order.status', 'Cancelled')
            ->distinct('order.order_id')
            ->count('order.order_id');
    }

    public function totalRefunded(): int
    {
        return DB::table('order')
            ->join('order_item', 'order.order_id', '=', 'order_item.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('product.seller_id', $this->seller_id)
            ->whereRaw('NOT "order".is_deleted')
            ->where('order.status', 'Refunded')
            ->distinct('order.order_id')
            ->count('order.order_id');
    }

    public function averageRating(): ?float
    {
        $avg = DB::table('review')
            ->join('product', 'review.product_id', '=', 'product.product_id')
            ->where('product.seller_id', $this->seller_id)
            ->avg('review.rating');

        return $avg ? round((float) $avg, 1) : null;
    }

    public function topSellingProducts(int $limit = 5): array
    {
        return $this->soldItemBaseQuery()
            ->select('product.product_id', 'product.name', DB::raw('SUM(order_item.quantity) as total'))
            ->groupBy('product.product_id', 'product.name')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->product_id => ['name' => $r->name, 'quantity' => (int) $r->total],
            ])
            ->toArray();
    }

    public function topCategories(int $limit = 7): array
    {
        return $this->soldItemBaseQuery()
            ->select('product.category', DB::raw('SUM(order_item.quantity) as total'))
            ->groupBy('product.category')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('total', 'category')
            ->toArray();
    }

    public function monthlyEarnings(): array
    {
        return $this->soldItemBaseQuery()
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

    public function purchaseFrequency(): array
    {
        return DB::table('order')
            ->join('order_item', 'order.order_id', '=', 'order_item.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('product.seller_id', $this->seller_id)
            ->whereRaw('NOT "order".is_deleted')
            ->select(
                DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(DISTINCT "order".order_id) as total')
            )
            ->groupBy(DB::raw("to_char(\"order\".ordered_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    }

    public function topReviewedProducts(int $limit = 5): array
    {
        return DB::table('review')
            ->join('product', 'review.product_id', '=', 'product.product_id')
            ->where('product.seller_id', $this->seller_id)
            ->select('product.product_id', 'product.name', DB::raw('COUNT(*) as total'))
            ->groupBy('product.product_id', 'product.name')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->mapWithKeys(fn($r) => [
                $r->product_id => ['name' => $r->name, 'count' => (int) $r->total],
            ])
            ->toArray();
    }

    public function earningsByCategory(): array
    {
        return $this->soldItemBaseQuery()
            ->select('product.category', DB::raw('SUM(product.price * order_item.quantity) as total'))
            ->groupBy('product.category')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'category')
            ->toArray();
    }

    public function topBuyers(int $limit = 5): array
    {
        return DB::table('order_item')
            ->join('order', 'order_item.order_id', '=', 'order.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->join('buyer', 'order.buyer_id', '=', 'buyer.buyer_id')
            ->where('product.seller_id', $this->seller_id)
            ->whereRaw('NOT "order".is_deleted')
            ->whereNotIn('order.status', ['Cancelled', 'Refunded'])
            ->select(
                DB::raw("CONCAT(buyer.first_name, ' ', buyer.last_name) as buyer_name"),
                DB::raw('SUM(product.price * order_item.quantity) as total')
            )
            ->groupBy('buyer.buyer_id', 'buyer.first_name', 'buyer.last_name')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get()
            ->pluck('total', 'buyer_name')
            ->toArray();
    }

    public function mostExpensiveProducts(int $limit = 5): array
    {
        return Product::where('seller_id', $this->seller_id)
            ->orderBy('price', 'desc')
            ->limit($limit)
            ->get()
            // Map the products to an associative array with product_id as the key and name/price as values
            ->mapWithKeys(fn($p) => [
                $p->product_id => ['name' => $p->name, 'price' => (float) $p->price],
            ])
            ->toArray();
    }

    public function leastExpensiveProducts(int $limit = 5): array
    {
        return Product::where('seller_id', $this->seller_id)
            ->active()
            ->orderBy('price', 'asc')
            ->limit($limit)
            ->get()
            ->mapWithKeys(fn($p) => [
                $p->product_id => ['name' => $p->name, 'price' => (float) $p->price],
            ])
            ->toArray();
    }

    public function orderStatusDistribution(): array
    {
        return DB::table('order')
            ->join('order_item', 'order.order_id', '=', 'order_item.order_id')
            ->join('product', 'order_item.product_id', '=', 'product.product_id')
            ->where('product.seller_id', $this->seller_id)
            ->whereRaw('NOT "order".is_deleted')
            ->select('order.status', DB::raw('COUNT(DISTINCT "order".order_id) as total'))
            ->groupBy('order.status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();
    }

    public function lowStockProducts(int $threshold = 5)
    {
        return Product::where('seller_id', $this->seller_id)
            ->active()
            ->where('quantity', '<', $threshold)
            ->orderBy('quantity')
            ->get();
    }
}
