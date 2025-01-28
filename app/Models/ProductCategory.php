<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    protected $table = 'product_category';

    public function color()
    {
        return $this->hasMany(ProductCategoryColor::class, 'category_id');
    }
}
