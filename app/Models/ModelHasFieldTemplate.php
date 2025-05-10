<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasFieldTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_template_id',
        'model_id',
        'model_type',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Relationship with FieldTemplate.
     */
    public function fieldTemplate()
    {
        return $this->belongsTo(FieldTemplate::class);
    }
}
