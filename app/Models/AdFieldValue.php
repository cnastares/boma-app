<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdFieldValue extends Model
{
    use HasFactory;

    protected $casts = [
        'value' => 'array',
    ];
    protected $fillable = [
        'ad_id',
        'field_id',
        'value'
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

}
