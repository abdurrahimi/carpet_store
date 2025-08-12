<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpbbMaster extends Model
{
    use HasFactory;
    protected $table = 'lpbb_master';

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'target_store');
    }
}
