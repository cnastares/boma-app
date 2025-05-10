<?php

namespace App\Models;

use App\Models\PlanFeature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Promotion extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'background_color',
        'text_color',
        'is_enabled'
    ];

    public function planFeatures()
    {
        return $this->hasMany(PlanFeature::class);
    }

    /**
     * Scope a query to only include enabled records.
     * @param mixed $query
     * @param mixed $value
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}
