<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedPackageItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ad_id',
        'order_package_item_id',
    ];
}
