<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FooterItem extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'footer_section_id', 'name', 'type', 'page_id', 'url', 'order', 'predefined_identifier'
    ];

    public function footerSection()
    {
        return $this->belongsTo(FooterSection::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
