<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'lang_code',
        'country',
        'title',
        'is_visible',
        'rtl',
        'icon'
    ];
}
