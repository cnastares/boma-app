<?php

namespace App\Models;

use App\Enums\FieldType;
use App\Enums\FieldValidationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Field extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;
    protected $fillable = [
        'required',
        'multiple',
        'options',
        'name',
        'type',
        'order',
        'listable',
        'filterable',
        'field_group_id',
        'helpertext',
        'default',
        'searchable',
        'validation_type',
        'max_length',
        'min_length'
    ];

    public $translatable = [
        'name',
        'helpertext'
    ];

    protected $casts = [
        'options' => 'array',
        'type' => FieldType::class,
        'validation_type' => FieldValidationType::class,
    ];


    public function fieldGroup()
    {
        return $this->belongsTo(FieldGroup::class, 'field_group_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_fields');
    }
    public function fieldTemplateMappings()
    {
        return $this->hasMany(FieldTemplateMappings::class, 'field_id');
    }

    public function fieldTemplates()
    {
        return $this->hasManyThrough(FieldTemplate::class, FieldTemplateMappings::class, 'field_id', 'id', 'id', 'field_template_id');
    }

    public function scopeSearchable($query)
    {
        return $query->where('searchable', 1);
    }
    public function scopeFilterable($query)
    {
        return $query->where('filterable', 1);
    }
    public function adFieldValues()
    {
        return $this->hasMany(AdFieldValue::class, 'field_id');
    }

}
