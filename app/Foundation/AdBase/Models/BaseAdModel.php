<?php

namespace App\Foundation\AdBase\Models;

use Adfox\ECommerce\Models\Warehouse;
use Adfox\ECommerce\Models\Inventory;
use Adfox\ECommerce\Models\ReturnPolicy;
use Adfox\ECommerce\Models\StockTransaction;
use App\Enums\AdInteractionType;
use App\Models\AdMetric;
use App\Models\Subscription;
use App\Models\UserTrafficSource;
use App\Notifications\TrendAlert;
use App\Settings\PaymentSettings;
use Approval\Traits\RequiresApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AdStatus;
use App\Models\AdCondition;
use App\Models\AdFieldValue;
use App\Models\AdInteraction;
use App\Models\AdType;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\CustomerReview;
use App\Models\FavouriteAd;
use App\Models\PageVisit;
use App\Models\PriceType;
use App\Models\Promotion;
use App\Models\UsedPackageItem;
use App\Models\User;
use App\Settings\AdSettings;
use App\Settings\WatermarkSettings;
use App\Traits\HasAdType;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\Unit;
use Spatie\Image\Image;

class BaseAdModel extends Model implements Sitemapable, HasMedia
{
    use HasFactory, InteractsWithMedia, HasUuids, SoftDeletes, RequiresApproval, HasAdType;

    protected $table = 'ads';

    protected $casts = [
        'status' => AdStatus::class,
        'tags' => 'array',
        'image_properties' => 'array',
        'view_history' => 'array',
        'posted_date' => 'datetime',
        'description_tiptap'=>'array'
    ];

    protected $fillable = [
        'title',
        'description',
        'price',
        'price_type_id',
        'condition_id',
        'posted_date',
        'user_id',
        'slug',
        'category_id',
        'for_sale_by',
        'city',
        'postal_code',
        'state',
        'country',
        'latitude',
        'longitude',
        'location_name',
        'tags',
        'type',
        'video_link',
        'phone_number',
        'display_phone',
        'status',
        'website_url',
        'image_properties',
        'view_count',
        'view_history',
        'website_label',
        'location_display_name',
        'source',
        'subscription_id',
        'price_suffix',
        'offer_price',
        'comment',
        'booking_address_id',
        'remaining_payment',
        'transaction_id',
        'tax',
        'whatsapp_number',
        'display_whatsapp',
        'ad_type_id',
        'child_category_id',
        'description_tiptap',
        'main_category_id',

    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Conditionally add fields based on the plugin
        if (isECommercePluginEnabled()) {
            $this->fillable = array_merge($this->fillable, [
                'stock_quantity',
                'sku',
                'return_policy_id',
                'enable_cash_on_delivery',
            ]);
        }

        if (has_plugin_vehicle_rental_marketplace() && is_vehicle_rental_active()) {
            $this->fillable = array_merge($this->fillable, [
                'make_id',
                'model_id',
                'availability_window',
                'min_trip_length',
                'max_trip_length',
                'transmission_id',
                'fuel_type_id',
                'mileage',
                'start_date',
                'end_date'
            ]);
        }
    }

    protected $appends = [
        'primaryImage',
        'mapLabel'
    ];

    public function toSitemapTag(): Url|string|array
    {
        return url('ad', $this->slug);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function getPrimaryImageAttribute(): ?string
    {
        $imageUrl = $this->getFirstMediaUrl('ads', 'thumb');
        // Check if the image URL is not empty
        if (!empty($imageUrl)) {
            return $imageUrl;
        }
        return getAdPlaceholderImage($this->id);
    }

    public function getOgImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('ads');
    }

    public function images(): array
    {

        return $this->getMedia('ads')->map(function ($media) {
            if (app(WatermarkSettings::class)->enable_watermark && $media->hasGeneratedConversion('watermark')) {
                return $media->getUrl('watermark');
            }
            return $media->getUrl();
        })->toArray();
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'ad_promotions')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $watermarkSettings = app(WatermarkSettings::class);

        $watermarkImagePath = getSettingMediaUrl('watermark.watermark_image', 'watermark', public_path('images/watermark.png'), true);

        $this->addMediaConversion('thumb')
        ->fit(Fit::Crop, 200, 200)
        ->nonQueued();

    $mediaPath = $media->getPath();

    if (!File::exists($mediaPath)) {
        \Log::error("Failed to read image file: {$mediaPath}");
        return;
    }

        if ($watermarkSettings->enable_watermark) {
            $alignPosition = match ($watermarkSettings->position) {
                'bottom-right' => AlignPosition::BottomRight,
                'bottom-left' => AlignPosition::BottomLeft,
                'top-right' => AlignPosition::TopRight,
                'top-left' => AlignPosition::TopLeft,
                'center' => AlignPosition::Center,
                default => AlignPosition::BottomRight,
            };

            try {
                $image = Image::load($mediaPath);
                $image->watermark(
                    $watermarkImagePath,
                    $alignPosition,
                    paddingX: 10,
                    paddingY: 10,
                    paddingUnit: Unit::Pixel
                )->save();
            } catch (\Exception $e) {
                \Log::error("Error processing image: " . $e->getMessage());
            }
        }
    }

    public function priceType()
    {
        return $this->belongsTo(PriceType::class);
    }

    public function condition()
    {
        return $this->belongsTo(AdCondition::class);
    }

    public function usedPackageItems()
    {
        return $this->hasMany(UsedPackageItem::class);
    }

    public function favouriteAds()
    {
        return $this->hasMany(FavouriteAd::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->favouriteAds()->count();
    }

    public function fieldValues()
    {
        return $this->hasMany(AdFieldValue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Function that defines the rule of when an approval process
     * should be actioned for this model.
     *
     * @param array $modifications
     *
     * @return boolean
     */
    protected function requiresApprovalWhen(array $modifications): bool
    {
        $isAdmin = auth()->user() && auth()->user()->is_admin;
        $isFilamentRequest = session()->has('filament'); // Detects if request is from Filament Admin;
        // Skip approval if edited from Filament Admin Panel
        if ($isAdmin && $isFilamentRequest) {
            return false;
        }
        if (app(AdSettings::class)->admin_approval_required && $this->status && $this->status->value != 'draft') {
            $approvalFields = ['title', 'description_tiptap'];
            foreach ($approvalFields as $field) {
                if (array_key_exists($field, $modifications)) {
                    return true;
                }
            }
        }
        // Handle some logic that determines if this change requires approval
        //
        // Return true if the model requires approval, return false if it
        // should update immediately without approval.
        return false;
    }

    public static function captureSave($item)
    {
        $diff = collect($item->getDirty())
            ->transform(function ($change, $key) use ($item) {
                return [
                    'original' => $item->getOriginal($key),
                    'modified' => $item->$key,
                ];
            })->all();
        $hasModificationPending = $item->modifications()
            ->whereNotNull('modifications->' . array_key_first($item->getDirty() ?? []))
            ->activeOnly()
            ->first();
        $modifier = $item->modifier();
        $modificationModel = config('approval.models.modification', \Approval\Models\Modification::class);
        $modification = $hasModificationPending ?? new $modificationModel();
        $modification->active = true;
        $modification->modifications = $diff;
        $modification->approvers_required = $item->approversRequired;
        $modification->disapprovers_required = $item->disapproversRequired;
        $modification->md5 = md5(json_encode($diff));

        if ($modifier && ($modifierClass = get_class($modifier))) {
            $modifierInstance = new $modifierClass();

            $modification->modifier_id = $modifier->{$modifierInstance->getKeyName()};
            $modification->modifier_type = $modifierClass;
        }

        if (is_null($item->{$item->getKeyName()})) {
            $modification->is_update = false;
        }

        $modification->save();

        if (!$hasModificationPending) {
            $item->modifications()->save($modification);
        }

        return false;
    }

    public function media(): MorphMany
    {
        return $this->morphMany(\App\Models\Media::class, 'model');
    }

    public function getOfferPercentage()
    {
        if (($this->offer_price && $this->adType?->enable_offer_price)) {
            $offerPercentage = null;
            if ($this->offer_price) {
                $discount = $this->price - $this->offer_price;
                $offerPercentage = round(($discount / $this->price) * 100);
                return $offerPercentage;
            }
        }

        return null;
    }

    public function isEnabledOffer()
    {
        return $this->adType?->enable_offer_price;
    }

    public function getMapLabelAttribute()
    {
        $ad = $this;
        $mapLabel = null;
        if ($this->adType?->disable_price_type != true) {
            $paymentSettings = app(PaymentSettings::class);
            $value = formatPriceWithCurrency($ad->price);
            $type_id = $ad->price_type_id;
            $label = $ad->priceType?->label;
            $has_prefix = $this->adType?->has_price_suffix;
            $price_suffix = $ad->price_suffix;
            $offer_enabled = $ad->isEnabledOffer();
            $offer_price = formatPriceWithCurrency($ad->offer_price);
            if ($type_id == 1) {
                // <!-- If offer is enabled -->
                if ($offer_enabled && $ad->offer_price) {
                    $mapLabel = $offer_price;
                    // <!-- Price suffix -->
                    if ($has_prefix && $price_suffix) {
                        $mapLabel .= '/' . $price_suffix;
                    }
                } else {
                    $mapLabel = $value;
                    if ($has_prefix && $price_suffix) {
                        $mapLabel .= '/' . $price_suffix;
                    }
                }
            } else {
                $mapLabel = $label;
            }

            return $mapLabel;
        }
    }

    public function returnPolicy()
    {
        return $this->belongsTo(ReturnPolicy::class, 'return_policy_id', 'id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'ad_id', 'id');
    }

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class, 'ad_id', 'id');
    }

    public function stock_transactions()
    {
        return $this->hasMany(StockTransaction::class, 'ad_id', 'id');
    }

    public function customerReviews()
    {
        return $this->morphMany(CustomerReview::class, 'reviewable');
    }

    public function vehicleBooking()
    {
        return $this->hasMany(\Adfox\VehicleRentalMarketplace\Models\VehicleCarBooking::class, 'ad_id', 'id');
    }

    public function features()
    {
        return $this->belongsToMany(\Adfox\VehicleRentalMarketplace\Models\VehicleFeature::class, 'ad_vehicle_feature', 'ad_id', 'vehicle_feature_id');
    }

    public function make()
    {
        return $this->belongsTo(\Adfox\VehicleRentalMarketplace\Models\VehicleMake::class);
    }

    public function transmission()
    {
        return $this->belongsTo(\Adfox\VehicleRentalMarketplace\Models\VehicleTransmission::class);
    }

    public function model()
    {
        return $this->belongsTo(\Adfox\VehicleRentalMarketplace\Models\VehicleModel::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(\Adfox\VehicleRentalMarketplace\Models\VehicleFuelType::class);
    }

    public function userTrafficSources()
    {
        return $this->morphMany(UserTrafficSource::class, 'trackable');
    }

    public function pageVisits()
    {
        return $this->morphMany(PageVisit::class, 'visitable');
    }

    public function adInteractions()
    {
        return $this->hasMany(AdInteraction::class);
    }

    public function conversations()
    {
        return $this->hasMany(related: Conversation::class);
    }

    /**
     *  The function sorts the ads in the following priority:
     *  1. Filter by category
     *  2. Highest number of conversations
     *  3. Highest view count
     *  4. Highest favorite count
     *  5. Highest total time spent on the page
     * @param mixed $query
     * @return mixed
     */
    public function scopePopular($query)
    {
        return $query->withCount(['favouriteAds', 'conversations'])
            ->withSum('pageVisits', 'time_spent_in_secs')
            ->orderByDesc('conversations_count')
            ->orderByDesc('view_count')
            ->orderByDesc('favourite_ads_count')
            ->orderByDesc('page_visits_sum_time_spent_in_secs');
    }

    public function monitorTrafficAndConversionTrends()
    {
        // Fetch the latest metric record for the ad or create a new one if none exists
        $lastMetric = AdMetric::where('ad_id', $this->id)->latest()->first();

        // Calculate current visits and conversion rate
        $totalVisits = $this->userTrafficSources->count();
        $totalConversions = $this->adInteractions()
            ->where('interaction_type', AdInteractionType::CHATCONTACT)
            ->count();

        $conversionRate = $this->calculateConversionRate($totalVisits, $totalConversions);

        // Thresholds for alerting
        $visitIncreaseThreshold = 50;
        $conversionDropThreshold = 10;

        // Check for visit and conversion rate trends
        $visitChange = $lastMetric ? ($totalVisits - $lastMetric->total_visits) : 0;
        $conversionRateDrop = $lastMetric ? ($lastMetric->conversion_rate - $conversionRate) : 0;

        // Send alerts if thresholds are met
        if ($visitChange >= $visitIncreaseThreshold && getSubscriptionSetting('status') && getUserSubscriptionPlan($this->user_id)?->automated_alerts) {
            $this->user->notify(new TrendAlert(__('messages.t_visit_spike_alert'), __('messages.t_sudden_increase_in_visits') . " +$visitChange."));
        }

        if ($conversionRateDrop >= $conversionDropThreshold && getSubscriptionSetting('status') && getUserSubscriptionPlan($this->user_id)?->automated_alerts) {
            $this->user->notify(new TrendAlert(__('messages.t_conversion_rate_drop_alert'), __('messages.t_conversion_rate_dropped_by') . " $conversionRateDrop%."));
        }

        // Store current metrics as the latest record
        AdMetric::create([
            'ad_id' => $this->id,
            'total_visits' => $totalVisits,
            'conversion_rate' => $conversionRate,
        ]);
    }

    protected function calculateConversionRate($totalVisits, $totalConversions)
    {
        if ($totalVisits === 0) {
            return 0; // Avoid division by zero
        }
        return round(($totalConversions / $totalVisits) * 100, 2); // Returns percentage
    }

    public function childCategory()
    {
        return $this->belongsTo(Category::class, 'child_category_id');
    }
    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'main_category_id');
    }
}
