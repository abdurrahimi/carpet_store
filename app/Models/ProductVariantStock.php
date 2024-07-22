<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariantStock extends Model
{
    use SoftDeletes;

    protected $table = 'products_variant_stock';
    protected $fillable = [
        'variant_id',
        'value',
        'type',
        'product_type',
        'stock_ref',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
