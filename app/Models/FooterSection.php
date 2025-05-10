<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FooterSection extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['title'];

    protected $fillable = [
        'title', 'type', 'predefined_identifier', 'order', 'column_span'
    ];

    public function footerItems()
    {
        return $this->hasMany(FooterItem::class);
    }
}
