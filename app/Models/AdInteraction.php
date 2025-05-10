<?php

namespace App\Models;

use App\Enums\AdInteractionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdInteraction extends Model
{
    use HasFactory;

    protected $fillable=[
        'interaction_type',
        'user_id'
    ];
    protected $casts=[
        'interaction_type'=>AdInteractionType::class
    ];

    public function ad(){
        return $this->belongsTo(Ad::class);
    }
}
