<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdMetric extends Model
{
    use HasFactory;
    protected $fillable = ['ad_id', 'total_visits', 'conversion_rate'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
