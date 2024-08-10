<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'store';

    public function manager()
    {
        return $this->belongsTo(Karyawan::class, 'manager_id');
    }
}
