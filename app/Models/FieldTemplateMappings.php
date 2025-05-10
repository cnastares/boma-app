<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldTemplateMappings extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_template_id',
        'field_id',
        'order',
        'default'
    ];
    public function field()
    {
        return $this->belongsTo(Field::class);
    }
    public function fieldTemplate()
    {
        return $this->belongsToMany(FieldTemplate::class, 'field_template_id');
    }
}
