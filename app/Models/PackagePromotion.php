<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PackagePromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id', 'promotion_id'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class);
    }
}
