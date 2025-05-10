<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class FieldGroup extends Model
{
    use HasFactory, SoftDeletes,HasTranslations;

    protected $fillable = [
        'name',
        'order',
    ];
    public $translatable = [
        'name',
    ];

    public function fields()
    {
        return $this->hasMany(Field::class, 'field_group_id');
    }

    public function fieldGroup()
    {
        return $this->belongsTo(FieldGroup::class, 'field_group_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_field'); // Assuming 'category_field' is the pivot table
    }

 
}
