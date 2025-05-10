<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PriceType extends Model
{
    use HasTranslations;

    use HasFactory;

    protected $translatable = ['name','label'];

    protected $fillable = [
        'name',
        'label'
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

}
