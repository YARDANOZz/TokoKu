<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type', // in, out, cancellation
        'quantity',
        'before_stock',
        'after_stock',
        'reference_type', // purchase, sale, sale_cancellation
        'reference_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'before_stock' => 'integer',
        'after_stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
