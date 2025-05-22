<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderApprovalLog extends Model
{
    use HasFactory;
    protected $table = 'order_approval_log';

    protected $fillable = [
        'user_id',
        'order_id',
        'detail',
        'status',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
