<?php

namespace App\Livewire\Ad;

use Adfox\VehicleRentalMarketplace\Models\TemproveryBookingData;
use Adfox\VehicleRentalMarketplace\Models\VehicleCarBooking;
use App\Enums\AdInteractionType;
use App\Jobs\UpdateUserSpendTime;
use App\Models\Ad;
use App\Models\AdFieldValue;
use App\Models\AdPromotion;
use App\Models\ContactAnalytic;
use App\Traits\StoresTrafficAndUtm;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use App\Models\Conversation;
use App\Models\ExternalAds;
use App\Models\FavouriteAd;
use App\Models\Field;
use App\Models\Message;
use App\Models\Promotion;
use App\Models\ReportedAd;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Settings\AdSettings;
use App\Settings\GeneralSettings;
use App\Settings\LiveChatSettings;
use App\Settings\SEOSettings;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Carbon\Carbon;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Http\Client\Request;
use Parsedown;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\On;


class AdDetails extends Component implements HasForms
{
    use SEOToolsTrait,InteractsWithForms,StoresTrafficAndUtm;

    // Properties
    public $ad;
    public $isFavourited = false;
    public $isFeatured = false;
    public $isUrgent = false;
    public $isWebsite = false;
    public $metaTitle;
    public $metaDescription;
    public $fieldDetails;
    public $descriptionHtml;
    #[Url(as: 'admin_view')]
    public $ownerView;
    public $referrer = false;

    public $breadcrumbs = [];
    public $relatedAds = [];

    public $data = [];

    public $tags;
    public $externalAds = [];


    public $startDate;
    public $endDate;
    public $startTime;
    public $endTime;
    public $vehicle;
    public $cartQuantity = 1;
    public $featureAdColors;
    public $urgentAdColors;
    /**
     * Mount the component with the provided ad details.
     *
     * @param Ad $ad The ad to display.
     */
    public function mount($slug)
    {
        $this->initializeAd($slug);
        $this->checkPromotions();
        $this->setSEOData();
        $this->buildBreadcrumbs();
        $this->fetchRelatedAds();
        $this->fetchTags();
        $this->recordView();
        $this->fetchExternalAds();
        $this->getPromotionColors();
        $this->form->fill([]);
    }

    /**
     * Record the view of the ad, ensuring it's counted once per user per ad using cookies.
     */
    protected function recordView()
    {
        // Generate a visitor identifier based on IP address and user agent
        $ip = request()->ip();
        $userAgent = request()->header('User-Agent');
        $visitorIdentifier = hash('md5', $ip . $userAgent);

        // Ensure the visitor identifier is stored in a cookie for persistence
        $cookieId = request()->cookie('visitor_id') ?? $visitorIdentifier;
        Cookie::queue('visitor_id', $cookieId, 525600);  // Valid for 1 year

        // Use the visitor cookie ID as the primary identifier
        $visitorIdentifier = $cookieId;

        // Check if a view from this visitor has been recorded recently
        $viewHistory = $this->ad->view_history ?? [];
        $viewHistoryArray = array_filter($viewHistory, 'is_array');
        $hasView = collect($viewHistoryArray)->contains(function ($view) use ($visitorIdentifier) {
            return $view['visitor_identifier'] === $visitorIdentifier;
        });

        if (!$hasView) {
            $this->incrementViewCount($visitorIdentifier);
        }
    }

    /**
     * Increment the view count of the ad and store the details of the view.
     */
    protected function incrementViewCount($visitorIdentifier)
    {
        $this->ad->increment('view_count');
        $this->storeViewDetails($visitorIdentifier);
    }

    /**
     * Store details of each view to track views over time for analytics purposes.
     */
    protected function storeViewDetails($visitorIdentifier)
    {
        $viewHistory = $this->ad->view_history ?? [];
        $viewHistory[] = [
            'viewed_at' => now()->toDateTimeString(),
            'visitor_identifier' => $visitorIdentifier,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent')
        ];
        $this->ad->view_history = $viewHistory;
        $this->ad->save();
    }

    /**
     * Initialize the ad details and handle potential access issues.
     *
     * @param Ad $ad The ad to display.
     */
    protected function initializeAd($slug)
    {
        $this->ad = Ad::where('slug', $slug)->first();
        if (is_vehicle_rental_active()) {
            $this->vehicle = \Adfox\VehicleRentalMarketplace\Models\VehicleRentalAd::where('slug', $slug)->first();
        }
        if (!$this->ad) {
            abort(404, 'Ad not found');
        }
        $this->checkAdAccess();
        $this->setDescriptionHtml();
        $this->fetchFieldDetails();
        $this->checkFavouriteStatus();
        if($this->ad){
            $this->storeUtmData($this->ad,request());
        }
    }

    protected function fetchTags()
    {
        if (!empty($this->ad->tags)) {
            $this->tags = collect($this->ad->tags) // Assuming $this->ad->tags is already an array
                ->map(function ($tag) {
                    $url = url('search') . '?query[sortBy]=date&query[search]=' . urlencode($tag);
                    return [
                        'name' => strtolower($tag),
                        'link' => $url
                    ];
                });
        } else {
            $this->tags = collect();
        }
    }


    /**
     * Ensure only owners or authorized individuals can see non-active ads.
     */
    protected function checkAdAccess()
    {
        $isActive = $this->ad->status->value === 'active';
        $isOwner = Auth::id() == $this->ad->user_id || $this->ownerView;
        $isAdmin = Auth::user()->is_admin;
        if (!$isActive && !$isOwner && !$isAdmin) {
            abort(404, 'Ad not found or inactive');
        }
        $this->ownerView = !$isActive && $isOwner;
    }

    /**
     * Convert the ad description to HTML.
     */
    protected function setDescriptionHtml()
    {
        $parsedown = new Parsedown();
        $this->descriptionHtml = $parsedown->text($this->ad->description);
    }

    /**
     * Fetch saved field details for the ad.
     */
    protected function fetchFieldDetails()
    {
        $this->fieldDetails = AdFieldValue::where('ad_id', $this->ad->id)
            ->with(['field', 'field.fieldGroup'])
            ->get()
            ->map(function ($adFieldValue) {
                return $this->transformFieldValue($adFieldValue);
            })
            ->groupBy(function ($item) {
                return $item['field_group_name'] ?? 'Other';
            })
            ->toArray();
    }

    /**
     * Transform the field value for easier use.
     *
     * @param AdFieldValue $adFieldValue The field value to transform.
     * @return array The transformed field value.
     */
    protected function transformFieldValue($adFieldValue)
    {
        if(!$adFieldValue->field){
            return [];
        }
        $fieldType = $adFieldValue->field->type->value;
        $value = $adFieldValue->value;
        $fieldGroupName = $adFieldValue->field->fieldGroup ? $adFieldValue->field->fieldGroup->name : null;

        if ($fieldType === 'select' || $fieldType === 'radio') {
            $options = $adFieldValue->field->options;
            $value = $options[$value] ?? $value;
        } elseif ($fieldType === 'checkbox') {
            $value = $value ? 'Yes' : 'No';
        } elseif ($fieldType === 'datetime') {
            $value = Carbon::parse($value)->format('d-M-y h:i:s');
        }

        return [
            'field_group_name' => $fieldGroupName,
            'field_name' => $adFieldValue->field->name,
            'field_id' => $adFieldValue->field->id,
            'value' => $value,
        ];
    }


    /**
     * Check the promotions associated with the ad.
     */
    protected function checkPromotions()
    {
        $currentDate = now();
        $this->isFeatured = $this->isPromotionActive(1, $currentDate);
        $this->isUrgent = $this->isPromotionActive(3, $currentDate);
        $this->isWebsite = $this->isPromotionActive(4, $currentDate);
    }

    /**
     * Check if a given promotion is active for the ad.
     *
     * @param int $promotionId The ID of the promotion to check.
     * @param string $currentDate The current date.
     * @return bool True if the promotion is active, false otherwise.
     */
    protected function isPromotionActive($promotionId, $currentDate)
    {
        return AdPromotion::where('ad_id', $this->ad->id)
            ->where('promotion_id', $promotionId)
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->exists();
    }


    /**
     * Check if the ad is favorited by the current user.
     */
    protected function checkFavouriteStatus()
    {
        $this->isFavourited = FavouriteAd::where('user_id', Auth::id())
            ->where('ad_id', $this->ad->id)
            ->exists();
    }

    /**
     * Begin a whatsapp chat with the ad seller.
     */
    public function chatWithWhatsapp()
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            Notification::make()
                ->title(__('messages.t_must_be_logged_to_chat'))
                ->danger()
                ->send();
            return redirect(route('login'));
        }

        // Check if user verification is required to post ads or chat
        $verificationRequired = app(AdSettings::class)->user_verification_required;
        $user = auth()->user();

        if ($verificationRequired && (!$user || !$user->verified)) {
            // Redirect to a verification required page if the user is not verified
            Notification::make()
                ->title(__('messages.t_verification_required_to_chat'))
                ->danger()
                ->send();
            return redirect()->route('verification-required');
        }

        // Get the authenticated user's ID
        $buyerId = Auth::id();

        // Prevent the owner of the ad from chatting with themselves
        if ($buyerId == $this->ad->user_id) {
            Notification::make()
                ->title(__('messages.t_cannot_chat_with_yourself'))
                ->danger()
                ->send();
            return;
        }

        // Construct the WhatsApp URL
        $whatsappUrl = "https://wa.me/" . $this->ad->user->whatsapp_number . "/?text=" . urlencode($this->ad->title);

        // Redirect to the WhatsApp URL
        return redirect()->away($whatsappUrl);
    }

    #[On('startDate')]
    public function updateStartDate($date)
    {
        $this->startDate = $date;
    }

    #[On('endDate')]
    public function updateEndDate($date)
    {
        $this->endDate = $date;
    }

    public function bookNowPage()
    {
        // Get the authenticated user's ID
        $buyerId = Auth::id();

        // Prevent the owner of the ad from chatting with themselves
        if ($buyerId == $this->ad->user_id) {
            Notification::make()
                ->title(__('messages.t_cannot_book'))
                ->danger()
                ->send();
            return;
        }

        // Check if user verification is required to post ads or chat
        $verificationRequired = app(AdSettings::class)->user_verification_required;
        $user = auth()->user();

        if ($verificationRequired && (!$user || !$user->verified)) {
            // Redirect to a verification required page if the user is not verified
            Notification::make()
                ->title(__('messages.t_verification_required_to_chat'))
                ->danger()
                ->send();
            return redirect()->route('verification-required');
        }

        if (!auth()->check()) {
            Notification::make()
                ->title(__('messages.t_must_be_logged_to_chat'))
                ->danger()
                ->send();
            return redirect(route('login'));
        }

        if (!isset($this->startDate) && !isset($this->endDate)) {
            Notification::make()
                ->title(__('messages.t_please_select_start_date_and_end_date'))
                ->danger()
                ->send();
            return;
        }

        if (!isset($this->startTime) && !isset($this->endTime)) {
            Notification::make()
                ->title(__('messages.t_please_select_start_time_and_end_time'))
                ->danger()
                ->send();
            return;
        }
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        if (Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1 < $this->ad->min_trip_length) {
            Notification::make()
                ->title(__('Minimum trip lenth is ' . $this->ad->min_trip_length . ' days'))
                ->danger()
                ->send();
            return;
        }

        if (Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1 > $this->ad->max_trip_length) {
            Notification::make()
                ->title(__('messages.t_max_trip_days', ['day' => $this->ad->max_trip_length]))
                ->danger()
                ->send();
            return;
        }

        // Check if any existing booking overlaps with the selected date range
        $overlappingBooking = VehicleCarBooking::where('ad_id', $this->ad->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($subQuery) use ($startDate, $endDate) {
                    // Case 1: Existing booking starts or ends within the selected range
                    $subQuery->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate]);
                })
                    ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                        // Case 2: Existing booking completely covers the selected range
                        $subQuery->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if (!$overlappingBooking) {
            $ramge = TemproveryBookingData::create([
                'ad_id' => $this->ad->id,
                'user_id' => auth()->id(),
                'start_date' => Carbon::parse($this->startDate . ' ' . $this->startTime),
                'end_date' => Carbon::parse($this->endDate . ' ' . $this->endTime),
                'days' => Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1
            ]);

            return redirect()->route('summary', ['ad_id' => $this->ad->id]);
        } else {
            Notification::make()
                ->title(__('please select available date range'))
                ->danger()
                ->send();
        }
    }
    /**
     * Begin a chat with the ad seller.
     *
     * @param string|null $messageContent The initial message content.
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse A redirection response.
     */
    public function chatWithSeller($messageContent = null)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            Notification::make()
                ->title(__('messages.t_must_be_logged_to_chat'))
                ->danger()
                ->send();
            return redirect(route('login'));
        }

        // Check if user verification is required to post ads or chat
        $verificationRequired = app(AdSettings::class)->user_verification_required;
        $user = auth()->user();

        if ($verificationRequired && (!$user || !$user->verified)) {
            // Redirect to a verification required page if the user is not verified
            Notification::make()
                ->title(__('messages.t_verification_required_to_chat'))
                ->danger()
                ->send();
            return redirect()->route('verification-required');
        }

        // Get the authenticated user's ID
        $buyerId = Auth::id();

        // Prevent the owner of the ad from chatting with themselves
        if ($buyerId == $this->ad->user_id) {
            Notification::make()
                ->title(__('messages.t_cannot_chat_with_yourself'))
                ->danger()
                ->send();
            return;
        }

        $this->saveContactInteraction(AdInteractionType::CHATCONTACT);
        // Check if a conversation already exists
        $conversation = Conversation::where('ad_id', $this->ad->id)
            ->where('buyer_id', $buyerId)
            ->where('seller_id', $this->ad->user_id)
            ->first();

        // If conversation does not exist, create one
        if (!$conversation) {
            $conversation = Conversation::create([
                'ad_id' => $this->ad->id,
                'buyer_id' => $buyerId,
                'seller_id' => $this->ad->user_id
            ]);
        }

        // If there's a message content passed, send it
        if ($messageContent) {
            $this->sendMessage($conversation->id, $this->ad->user_id, $messageContent);
        }
        // Redirect to the messaging page with the conversation_id
        if (app('filament')->hasPlugin('live-chat') && app(LiveChatSettings::class)->enable_livechat) {
            return redirect('/messages/' . $conversation->id);
        } else {
            return redirect('/my-messages?conversation_id=' . $conversation->id);
        }
    }

    /**
     * Save data of interaction who contact via ad details page
     * @return void
     */
    public function saveContactInteraction($interactionType){
        $this->ad->adInteractions()->create([
            'user_id'=>auth()->id(),
            'interaction_type'=>$interactionType
        ]);
    }

    /**
     * Send a message within a conversation.
     *
     * @param int $conversationId The ID of the conversation.
     * @param int $receiverId The ID of the receiver.
     * @param string $content The content of the message.
     */
    public function sendMessage($conversationId, $receiverId, $content)
    {
        Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'content' => $content
        ]);
    }

    /**
     * Add or remove the ad from the user's favorites.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse A redirection response.
     */
    public function addToFavourites()
    {
        // Check if the user is not logged in
        if (!Auth::check()) {
            // If not logged in, redirect to login page or show a message.
            Notification::make()
                ->title(__('messages.t_login_to_add_favorites'))
                ->success()
                ->send();
            return redirect(route('login'));
        }

        // Check if already added to favorites
        $favourite = FavouriteAd::where('user_id', Auth::id())
            ->where('ad_id', $this->ad->id)
            ->first();

        if ($favourite) {
            $favourite->delete();
            $this->isFavourited = false;
        } else {
            FavouriteAd::create([
                'user_id' => Auth::id(),
                'ad_id' => $this->ad->id,
            ]);
            $this->isFavourited = true;
        }
    }

    /**
     * Builds the breadcrumb trail based on the ad's category and subcategory.
     */
    protected function buildBreadcrumbs()
    {
        // Start with the home breadcrumb
        $this->breadcrumbs['/'] = 'Home';

        $category = null;
        $subCategory = null;

        // If the ad has a subcategory, add it to the breadcrumbs
        if ($this->ad->category && $this->ad->category->parent) {
            $subCategory = $this->ad->category;
            $category = $this->ad->category->parent;
            $this->breadcrumbs['/categories/' . $category->slug] = $category->name;
            $this->breadcrumbs['/categories/' . $category->slug . '/' . $subCategory->slug] = $subCategory->name;
        }
    }


    /**
     * Define the form for reporting an advertisement.
     *
     * This form allows users to report an ad by providing a reason,
     * ensuring that ads maintain the platform's standards and guidelines.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('reason')
                    ->label(__('messages.t_report_reason_label'))
                    ->placeholder(__('messages.t_report_reason_placeholder'))
                    ->required()
                    ->maxLength(500)
                    ->helperText(__('messages.t_report_reason_helper'))
            ])
            ->statePath('data');
    }

    /**
     * Perform common checks before reporting an ad.
     *
     * @return bool Indicates if the process should continue.
     */
    private function performReportAdChecks()
    {
        if (auth()->guest()) {
            Notification::make()
                ->title(__('messages.t_login_or_signup_to_report_ad'))
                ->info()
                ->send();
            return false;
        }

        if ($this->ad->user_id == auth()->id()) {
            Notification::make()
                ->title(__('messages.t_cannot_report_own_ad'))
                ->danger()
                ->send();
            return false;
        }

        return true;
    }


    /**
     * Open the report ad modal if the user is authenticated and not reporting their own ad.
     *
     * This function checks if the user is logged in. If not, it redirects them to the login page
     * with a notification encouraging them to log in or sign up to report the ad. It also ensures
     * that users cannot report their own ads by showing a notification if they attempt to do so.
     */
    public function openReportAd()
    {
        if (!$this->performReportAdChecks()) {
            return;
        }
        // Dispatching open modal event
        $this->dispatch('open-modal', id: 'report-ad');
    }

    /**
     * Submit a report for an advertisement.
     *
     * Creates a new record in the ReportedAd model with details about the report.
     */
    public function reportAd()
    {

        if (!$this->performReportAdChecks()) {
            return;
        }
        $data = $this->form->getState();
        if(isset($data['reason'])){
            // Create a new reported ad record
            ReportedAd::create([
                'user_id' => Auth::id(),
                'ad_id' => $this->ad->id, // Assuming $this->ad contains the Ad model instance
                'reason' => $data['reason']
            ]);
        }
        // Show success notification
        Notification::make()
            ->title(__('messages.t_report_submitted_successfully'))
            ->success()
            ->send();

        // Dispatching open modal event
        $this->dispatch('close-modal', id: 'report-ad');
    }

    /**
     * Fetches related ads based on category and tags of the current ad.
     * Excludes the current ad and limits the result set for relevance and performance.
     */
    public function fetchRelatedAds()
    {
        $currentAd = $this->ad;

        // Start the query
        $query = Ad::query();

        // Always include the category match
        $query->where('category_id', $currentAd->category_id);

        // Filter only active ads
        $query->where('status', 'active');

        // Exclude the current ad and limit the results
        $relatedAds = $query->where('id', '!=', $currentAd->id)
            ->limit(10)
            ->get();

        $this->relatedAds = $relatedAds;
    }

    public function fetchExternalAds()
    {
        $externalAds = ExternalAds::where('status', true)->get();
        return $externalAds;
    }

    public function addToCart()
    {
        self::setMaxQuantityValue();

        if (auth()->check()) {
            $cart = auth()->user()->carts()->where('ad_id', $this->ad->id)->first();

            if ($cart) {
                $cart->update(['quantity' => $this->cartQuantity]);

                Notification::make()
                    ->title(__('messages.t_updated_successfully'))
                    ->success()
                    ->send();
            } else {
                auth()->user()->carts()->create([
                    'vendor_id' => $this->ad->user_id,
                    'ad_id' => $this->ad->id,
                    'quantity' => $this->cartQuantity,
                ]);

                Notification::make()
                    ->title(__('messages.t_saved_successfully'))
                    ->success()
                    ->send();
            }
        } else {
            $cart = session()->get('cart', []);

            $cart[] = [
                'cart_id'=>(string) Str::uuid(),
                'vendor_id' => $this->ad->user_id,
                'ad_id' => $this->ad->id,
                'quantity' => $this->cartQuantity,
            ];

            session()->put('cart', $cart);
        }
    }

    public function buyNow()
    {
        self::setMaxQuantityValue();

        if (auth()->check()) {
            $cart = auth()->user()->carts()->where('ad_id', $this->ad->id)->first();

            if ($cart) {
                $cart->update(['quantity' => $this->cartQuantity]);

                Notification::make()
                    ->title(__('messages.t_updated_successfully'))
                    ->success()
                    ->send();
            } else {
                $cart = auth()->user()->carts()->create([
                    'vendor_id' => $this->ad->user_id,
                    'ad_id' => $this->ad->id,
                    'quantity' => $this->cartQuantity,
                ]);

                Notification::make()
                    ->title(__('messages.t_saved_successfully'))
                    ->success()
                    ->send();
            }

            return redirect()->route('reservation.cart-summary', $cart->id);
        } else {
            return redirect()->route('login');
        }
    }

    private function setMaxQuantityValue()
    {
        $this->cartQuantity = empty($this->cartQuantity) ? 1 : $this->cartQuantity;

        if ($this->cartQuantity > getECommerceMaximumQuantityPerItem()) {
            Notification::make()
                ->title(__('messages.t_cart_max_limit_reached', ['max' => getECommerceMaximumQuantityPerItem()]))
                ->warning()
                ->send();

            $this->cartQuantity = getECommerceMaximumQuantityPerItem() ?? 1;
        }
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

        UpdateUserSpendTime::dispatch($userSpendTimeData,$this->ad,);
    }

    #[On('websiteURLClicked')]
    public function websiteURLClicked(){
        $this->saveContactInteraction(AdInteractionType::EXTERNALLINKCLICK);
    }

    public function getPromotionColors()
    {
        $this->featureAdColors = Promotion::find(1);
        $this->urgentAdColors = Promotion::find(3);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View The view to render.
     */
    public function render()
    {
        return view('livewire.ad.ad-details');
    }
    /**
     * Set SEO data
     */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);


        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? app_name();

        $title = $this->ad->title . " $separator " . $siteName;
        $description = $this->ad->description ?? app_name();
        $ogImage = $this->ad->og_image;
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
        $this->seo()->setCanonical(url()->current());
        $this->seo()->opengraph()->setTitle($title);
        $this->seo()->opengraph()->setDescription($description);
        $this->seo()->opengraph()->setUrl(url()->current());
        $this->seo()->opengraph()->setType('website');
        $this->seo()->opengraph()->addImage($ogImage);
        $this->seo()->twitter()->setImage($ogImage);
        $this->seo()->twitter()->setUrl(url()->current());
        $this->seo()->twitter()->setSite("@" . $seoSettings->twitter_username);
        $this->seo()->twitter()->addValue('card', 'summary_large_image');
        $this->seo()->metatags()->addMeta('fb:page_id', $seoSettings->facebook_page_id, 'property');
        $this->seo()->metatags()->addMeta('fb:app_id', $seoSettings->facebook_app_id, 'property');
        $this->seo()->metatags()->addMeta('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1', 'name');
        $this->seo()->jsonLd()->setTitle($title);
        $this->seo()->jsonLd()->setDescription($description);
        $this->seo()->jsonLd()->setUrl(url()->current());
        $this->seo()->jsonLd()->setType('WebSite');
    }
}
