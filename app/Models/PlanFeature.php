<?php

namespace App\Models;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PlanFeature extends Model
{
    use HasFactory,HasTranslations;
    protected $fillable = [
        'plan_id',
        'name',
        'description',
        'type',
        'value',
        'resettable_period',
        'resettable_interval',
        'sort_order',
        'promotion_id'
    ];

    public $translatable = [
        'name',
        'description'
    ];

    public function plan(){
        return $this->belongsTo(Plan::class,'plan_id');
    }
    public function scopeType($query,$type){
        $query->where('type',$type);
    }

    public function scopePromotion($query,$promotionId){
        $query->where('type','promotion')->where('promotion_id',$promotionId);
    }
    // protected function name(): Attribute
    // {
    //     return Attribute::make(
    //         get: function (string $value) {
    //              ucfirst($value)
    //             },
    //     );
    // }
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
    public function scopeActiveSubscription($query){
        return $query->whereHas('plan.subscriptions',function($query){
            return $query->active()->whereDate('ends_at', '>=', today());
        });
    }
}
