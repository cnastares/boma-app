<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;
    protected $fillable=[
        'time_spent_in_secs',
        'ip_address',
        'device',
        'browser',
        'user_id'
    ];

    public function visitable(){
        return $this->morphTo();
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
