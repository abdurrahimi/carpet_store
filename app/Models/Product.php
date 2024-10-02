<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supp_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted()
    {
        // Set created_by saat create data
        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        // Set updated_by saat update data
        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        // Set deleted_by saat delete data (soft delete)
        static::deleting(function ($model) {
            $model->deleted_by = Auth::id();
            $model->save(); // Kalau lo pakai soft delete
        });
    }
}
