<?php

namespace App\Models;

use App\Models\Ad;
use App\Models\AdPromotion;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'subscriber_id',
        'subscriber_type',
        'plan_id',
        'subscription_reference',
        'payment_method',
        'trial_ends_at',
        'status',
        'starts_at',
        'ends_at',
        'cancels_at',
        'paused_at',
        'gateway_data',
        'metadata',
        'ad_count',
        'feature_ad_count',
        'urgent_ad_count',
        'spotlight_ad_count',
        'website_url_count',
        'price',
        'is_admin_granted'
    ];
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancels_at' => 'datetime',
        'paused_at' => 'datetime',
        'gateway_data' => 'array',
        'metadata' => 'array',
    ];

    public function subscriber()
    {
        return $this->morphTo();
    }

    public function getSubscribernameAttribute(){
        return $this->subscriber->name ?? 'dinesh';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeInactive($query)
    {
        return $query->where('status', '!=', 'active');
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function getRemainAdCount()
    {
        $adCount = $this->ads()->active()->count();
        return getAdLimit() - $adCount;
    }

    public function getActiveAds()
    {
        return $this->ads()->active()->get();
    }

    public function adPromotions()
    {
        return $this->hasMany(AdPromotion::class);
    }

    public function getActiveAdPromotions()
    {
        return $this->adPromotions()
            ->active()
            ->where('end_date', '>=', today())
            ->get();
    }

    public function getAdPromotionCount($promotionId)
    {
        $plan = $this->plan()->with([
            'features' => function ($query) use ($promotionId) {
                $query->where('type', 'promotion')->where('promotion_id', $promotionId);
            }
        ])->first();
        $adPromotionCount = $plan?->features?->first()->value ?? 0;
        return $adPromotionCount;
    }

    public function getRemainAdPromotionCount($promotionId)
    {
        $activeAdPromotionCount = $this->getActiveAdPromotionCount($promotionId);
        return $this->getAdPromotionCount($promotionId) - $activeAdPromotionCount;
    }

    public function getActiveAdPromotionCount($promotionId)
    {
        return $this->adPromotions()
            ->active()
            ->where('end_date', '>=', today())->whereHas('promotion', function ($query) use ($promotionId) {
                $query->whereId($promotionId);
            })->count();
    }
}
