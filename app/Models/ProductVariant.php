<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $table = 'products_variant';
    protected $fillable = [
        'product_id',
        'name',
        'color',
        'length',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stocks()
    {
        return $this->hasMany(ProductVariantStock::class, 'variant_id');
    }
}
