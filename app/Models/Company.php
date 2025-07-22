<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';

    protected $fillable = [
        'name', 'address', 'logo','phone', 'bank_name', 'bank_account_number', 'bank_account_holder', 'created_at', 'updated_at'
    ];
}