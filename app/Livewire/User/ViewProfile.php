<?php

namespace App\Livewire\User;

use App\Jobs\UpdateUserSpendTime;
use App\Models\Ad;
use App\Models\Category;
use App\Models\StoreBanner;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Notifications\Notification;
use App\Traits\StoresTrafficAndUtm;
use Illuminate\Support\Carbon;

/**
 * ViewProfile Component.
 * Displays the profile of a specific user along with their associated ads.
 */
class ViewProfile extends Component
{
    use SEOToolsTrait,StoresTrafficAndUtm;

    // Represents the ads associated with the user.
    // public $ads;

    public $breadcrumbs = [];

    // Represents the User model instance.
    public $user;
    public $ads;
    public $userId;
    public $filteredAds=[];
    public $selectedCategory;
    #[Url(keep:true,as:'category')]
    public $categorySlug;
    public $categories = [];
    #[Url(keep:true,as:'sort-by')]
    public $sortBy ;
    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    public $search ;
    public $followersList = [];
    public $followingList = [];
    public $showFollowers = false;
    public $banners = [];
    public $publishedAdCount = 0;
    public $soldAdCount = 0;
    /**
     * Mount lifecycle hook.
     * Fetches the user details and their ads based on the provided id.
     *
     * @param int $id The ID of the user whose profile is to be displayed.
     */
    public function mount($id)
    {
        $this->userId=$id;
        $this->fetchUser();
        $this->fetchAdsForUser();
        $this->setSeoData();
        $this->buildBreadcrumbs();
        $this->fetchBanners();
    }

    /**
     * Fetches the user details based on the provided id.
     *
     *
     */
    protected function fetchUser()
    {
        $this->user = User::with('banners')->findOrFail($this->userId);
        if($this->user){
            $this->storeUtmData($this->user,request());
        }
    }

    /**
     * Fetches the ads associated with the provided id.
     *
     *
     */
    protected function fetchAdsForUser()
    {
        if ($this->user) {
            $this->ads = Ad::whereIn('status', ['active', 'sold'])
                ->where('user_id', $this->userId)
                ->get();

            //get data for last 90 days
            $startDate=Carbon::now()->subDays(90);
            $endDate=Carbon::now();
            $this->publishedAdCount=Ad::where('status', ['active', 'sold'])
                ->whereBetween('posted_date', [$startDate, $endDate])
                ->where('user_id', $this->userId)
                ->count();
            $this->soldAdCount=Ad::where('status', 'sold')
                ->where('user_id', $this->userId)
                ->count();

            if ($this->ads && count($this->ads)) {
                $categoryIds = $this->ads->pluck('category_id');
                // $this->categories = Category::whereHas('subcategories', function ($query) use ($categoryIds) {
                //     $query->whereIn('id', $categoryIds);
                // })->whereNull('parent_id')->get()->sortBy('order');
                $this->categories = Category::whereIn('id', $categoryIds)->get();
                $this->updateAdData();
            }
        }
    }
    /**
     * Retrieve a list of advertisements based on applied filters.
     *
     *
     */
    #[On('update-ad-data')]
    public function updateAdData()
    {
        $query = Ad::query()->whereIn('status', ['active', 'sold'])
        ->where('user_id', $this->userId);
        if ($this->categorySlug) {
            $category = Category::where('slug', $this->categorySlug)->first();
            if($category){
                $query->where('category_id', $category->id);
            }
        }
         // Sorting logic based on 'sortBy' filter
         if (isset($this->sortBy) && $this->sortBy) {
            switch ($this->sortBy) {
                case 'date':
                    $query->orderBy('created_at', 'desc'); // For the newest ads first
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc'); // For price from Low to High
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc'); // For price from High to Low
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc'); // For the newest ads first
                    break;
            }
        }
         // Add string-based search logic
         if (isset($this->search) && $this->search) {
            $searchQuery = $this->search;

            // Define which columns to search in
            $query->where(function ($query) use ($searchQuery) {
                $query->where('title', 'like', '%' . $searchQuery . '%')  // Search in 'title' column
                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('tags', 'like', '%' . $searchQuery . '%');
            });
        }
        $this->filteredAds=$query->get();
    }

    public function fetchBanners(){
        $bannerCount=getSubscriptionSetting('status')  ? getUserSubscriptionPlan($this->userId)?->banner_count :0;
        $this->banners=$this->user->banners->take($bannerCount);
    }
    /**
     * Set SEO data
     */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);

        $seoData=$this->getSeoDataForUser();

        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? app_name();
        if($seoData){
            $title = $seoData->meta_title . " $separator " . $siteName;
            $description = $seoData->meta_description;
            $keyWords = $seoData->meta_keywords;
            $ogImage=$seoData->og_image;
        }else{
            $title = $this->user->name . " $separator " . $siteName;
            $description = $seoSettings->meta_description;
            $ogImage = getSettingMediaUrl('seo.ogimage', 'seo', asset('images/ogimage.jpg'));
        }
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
        if(isset($keyWords)){
            $this->seo()->metatags()->setKeyWords($keyWords);
        }
        $this->seo()->opengraph()->setTitle($title);
        $this->seo()->opengraph()->setDescription($description);
        $this->seo()->opengraph()->setUrl(url()->current());
        $this->seo()->opengraph()->setType('website');
        $this->seo()->opengraph()->addImage($ogImage);
    }

    public function getSeoDataForUser(){
        return $this->user->seo()->first();
    }
    /**
     * Builds the breadcrumb trail for the profile page.
     */
    private function buildBreadcrumbs()
    {
        // Start with the home breadcrumb
        $this->breadcrumbs['/'] = __('messages.t_home');

        // Add the profile breadcrumb
        if ($this->user) {
            $profileLabel = __('messages.t_user_profile', ['name' => $this->user->name]);
            $this->breadcrumbs['/profile/' . $this->user->slug . '/' . $this->user->id] = $profileLabel;
        }
    }

    public function updatedSortBy(){
        $this->dispatch('update-ad-data');
    }
    public function showFollowersModal()
    {
        $this->showFollowers = true;
        $this->followersList = $this->user->followers()->get();
        $this->dispatch('open-modal', id: 'follow-modal');
    }

    public function showFollowingModal()
    {
        $this->showFollowers = false;
        $this->followingList = $this->user->following()->get();
        $this->dispatch('open-modal', id: 'follow-modal');
    }

    public function toggleFollow()
    {
        if (auth()->guest()) {
            Notification::make()
                ->title(__('messages.t_login_or_signup_to_follow'))
                ->info()
                ->send();
            return false;
        }

        if ($this->user->id == auth()->id()) {
            Notification::make()
                ->title(__('messages.t_cannot_follow_own_profile'))
                ->info()
                ->send();
            return;
        }

        if ($this->user->followers()->where('follower_id', auth()->id())->exists()) {
            // User is already following, so unfollow
            $this->user->followers()->detach(auth()->id());
        } else {
            // User is not following, so follow
            $this->user->followers()->attach(auth()->id());
        }
    }

    public function getFollowersCountProperty()
    {
        return $this->user->followers()->count();
    }

    public function getFollowingCountProperty()
    {
        return $this->user->following()->count();
    }

    public function isFollowing()
    {
        return auth()->check() && $this->user->followers()->where('follower_id', auth()->id())->exists();
    }
    /**
     * Update user spend time on store page
     *
     * @param integer $timeSpentInSeconds
     * @return void
     */
    #[On('saveTimeSpend')]
    public function saveTimeSpend($timeSpentInSeconds){
        $userSpendTimeData=[
            'time_spent_in_secs'=>$timeSpentInSeconds,
            'ip_address'=> getClientIp(),
            'user_id' => auth()->id(),     // Include user agent
        ];

        UpdateUserSpendTime::dispatch($userSpendTimeData,$this->user);
    }

    public function updateClickCount($bannerId)
    {
        $banner = StoreBanner::whereId($bannerId)->first();
        if ($banner) {
            $banner->increment('clicks');
            $banner->save();
            // $banner->increment('clicks');
            $link = $banner->link;
            if ($link) {
                return $this->js("setTimeout(() => {window.open(" . "'" . $link . "'" . ", '_blank')})");
            }
        }
    }
    #[On('update-banner-view')]
    public function updateBannerView($bannerId)
    {
        $banner = StoreBanner::whereId($bannerId)->first();
        if ($banner) {
            $banner->increment('views');
            $banner->save();
        }
    }

    /**
     * Renders the ViewProfile view.
     */
    public function render()
    {
        return view('livewire.user.view-profile',['filteredAds' => $this->filteredAds ]);
    }
}
