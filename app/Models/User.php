<?php

namespace App\Models;

use App\Models\Reservation\Cart;
use App\Models\Reservation\Location;
use Adfox\VehicleRentalMarketplace\Models\BookingAddress;
use App\Models\Wallets\Wallet;
use App\Observers\UserObserver;
use App\Traits\HasSubscriptions;
use Approval\Traits\ApprovesChanges;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasMedia, HasAvatar
{

    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasUuids, SoftDeletes, HasSubscriptions, ApprovesChanges, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'about_me',
        'phone_number',
        'is_admin',
        'account_type',
        'facebook_id',
        'google_id',
        'dynamic_fields',
        'suspended',
        'facebook_link',
        'twitter_link',
        'instagram_link',
        'linkedin_link',
        'gender',
        'date_of_birth',
        'country',
        'state',
        'city',
        'business_hours',
        'whatsapp_number',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dynamic_fields' => 'array',
        'business_hours' => 'array',
    ];

    protected $appends = [
        'bannerImage',
    ];

    public function ads()
    {
        return $this->hasMany(related: Ad::class);
    }

    public function bookinglocations()
    {
        return $this->hasMany(BookingAddress::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function favouritesAds()
    {
        return $this->hasMany(FavouriteAd::class);
    }


    public function getSlugAttribute(): string
    {
        $slug = urlencode(Str::lower(str_replace(' ', '-', $this->name)));

        return $slug;
    }

    public function canAccessPanel(Panel $panel): bool
    {

        $panelId = $panel->getId();

        if ($panelId === 'admin') {
            return $this->is_admin || $this->roles()->exists();
        } elseif ($panelId === 'app') {
            return true;
        }
        // if (request()->is('admin*')) {
        // } else {
        // }
    }

    public function getProfileImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('profile_images');
    }

    /**
     * Get user avatar
     *
     * @return object
     */
    public function getAvatarAttribute()
    {
        return $this->getFirstMediaUrl('profile_images');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    /**
     * Get the verification status of the user.
     *
     * @return bool
     */
    public function getVerifiedAttribute(): bool
    {
        $verification = $this->verificationCenter()->first();
        return $verification ? $verification->status === 'verified' : false;
    }

    /**
     * Get the user's verification center data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function verificationCenter()
    {
        return $this->hasOne(VerificationCenter::class);
    }

    public function mobileVerificationCode()
    {
        return $this->hasMany(MobileVerificationCode::class);
    }

    public function feedbackCount()
    {
        $feedbackAsSeller = $this->hasMany(Feedback::class, 'seller_id')->count();
        return $feedbackAsSeller;
    }

    public function verification()
    {
        return $this->hasOne(VerificationCenter::class, 'user_id');
    }


    /**
     * Get the dynamic fields as a formatted string.
     *
     * @return string
     */
    public function getDynamicFieldsListAttribute(): string
    {
        $fields = $this->dynamic_fields;

        // Check if the decoding was successful and the result is an array
        if (is_array($fields) && !empty($fields)) {
            // Transform the array into the desired string format
            return collect($fields)->map(function ($field) {
                // Check if both 'name' and 'value' keys exist
                if (isset($field['name'], $field['value'])) {
                    // Format as "Field name - Field value"
                    return "{$field['name']} - {$field['value']}";
                }
                return null;
            })->filter()->implode(', '); // Filter out any null values and implode into a string separated by commas
        }

        return '-'; // Default return value if there are no fields or if there's an error
    }

    /**
     * Get the count of active ads .
     *
     * @return int
     */
    public function getActiveAdCount()
    {
        return $this->ads()->active()->count();
    }

    /**
     * Get the count of active ads .
     *
     * @return
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the count of active ads .
     *
     * @return int
     */
    public function getActiveSubscriptionAdCount()
    {
        return $this->ads()->active()->source('subscription')->count();
    }

    /**
     * Get the count of free ads .
     *
     * @return int
     */
    public function getFreeAdCount()
    {
        return $this->ads()->active()->source('free')->count();
    }

    protected function authorizedToApprove(\Approval\Models\Modification $mod): bool
    {
        // Return true to authorize approval, false to deny
        return $this->is_admin;
    }
    public function getBannerImageAttribute()
    {
        return $this->getFirstMedia('user_banner_images');
    }
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('responsive-banner')
            ->quality(100)
            ->withResponsiveImages()
            ->nonQueued();
    }
    public function banners()
    {
        return $this->hasMany(StoreBanner::class);
    }

    /**
     * Get feedback that the user has given to others (as a buyer).
     */
    public function feedbacksGiven()
    {
        return $this->hasMany(Feedback::class, 'buyer_id');
    }

    /**
     * Get feedback that the user has received from others (as a seller).
     */
    public function feedbacksReceived()
    {
        return $this->hasMany(Feedback::class, 'seller_id');
    }

    /**
     * Get rating that user has received from others
     * @return string
     */
    public function getRatingAttribute()
    {
        $ratings = $this->feedbacksReceived;
        $rating = 0;
        if ($ratings && $this->feedbacksReceived->count()) {
            $rating = $ratings->sum('rating') / $this->feedbacksReceived->count();
        }
        return number_format($rating, 1);
    }

    /**
     * Seo for User/Store Page
     *
     */
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function userTrafficSources()
    {
        return $this->morphMany(UserTrafficSource::class, 'trackable');
    }

    public function pageVisits()
    {
        return $this->morphMany(PageVisit::class, 'visitable');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_image;
    }
}
