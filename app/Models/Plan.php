<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasFactory,HasTranslations;
    // Define the table associated with the model
    protected $table = 'plans';

    // Define the attributes that are mass assignable
    protected $fillable = [
        // Basic plan information
        'slug', 'name', 'description', 'price', 'currency', 'is_active','is_free',

        // Subscription details
        'signup_fee', 'trial_period', 'trial_interval', 'invoice_period', 'invoice_interval',
        'grace_period', 'grace_interval', 'prorate_day', 'prorate_period', 'prorate_extend_due',

        // Limits and counts
        'ad_count', 'feature_ad_count', 'urgent_ad_count', 'spotlight_ad_count',
        'website_url_count', 'images_limit', 'chat_limit', 'banner_count','delete_free_ads','free_listing_duration_days',

        // Feature flags
        'ads_level', 'video_posting', 'enable_user_profile_description', 'enable_social_media_links',
        'enable_business_hours','enable_location', 'rating', 'number_of_visits', 'number_of_favorites',
        'average_view_time', 'automated_alerts', 'automated_messages', 'automated_email_marketing',

        // Analysis and tools
        'filter_options_level', 'seo_tools_level', 'utm_parameters_level', 'traffic_source',
        'contact_conversion_rate_level', 'demographic_analysis_level', 'product_engagement_level',
        'product_performance_analysis', 'boost_analysis', 'custom_reports_level', 'clicks_on_link',

        // External IDs
        'price_id', 'stripe_product_id', 'paypal_plan_id',

        // Order and sorting
        'sort_order'
    ];


    public $translatable = [
        'name',
        'description'
    ];
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class,'plan_id');
    }
    public function features()
    {
        return $this->hasMany(PlanFeature::class,'plan_id');
    }

    public function scopeActive($query){
        $query->where('is_active',true);
    }

    public function scopeFree($query){
        return $query->where('is_free',true);
    }
}
