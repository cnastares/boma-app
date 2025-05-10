<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AdCondition extends Model
{
    use HasTranslations;

    use HasFactory;

    protected $translatable = ['name'];

    protected $fillable = [
        'name',
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }
}
