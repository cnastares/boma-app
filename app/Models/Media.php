<?php
namespace App\Models;

use App\Models\Scopes\ActiveMediaScope;
use App\Observers\MediaObserver;
use App\Settings\AdSettings;
use Approval\Models\Modification;
use Approval\Traits\RequiresApproval;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Support\Facades\Session;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

// #[ScopedBy([ActiveMediaScope::class])]
#[ObservedBy(MediaObserver::class)]
class Media extends BaseMedia
{
    use RequiresApproval;

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
        // $approvalFields=['file_name','mime_type','disk','size'];
        if (app(AdSettings::class)->admin_approval_required) {
            if (isset($modifications['model_type']) && $modifications['model_type'] == 'App\Models\Ad' && isset($modifications['model_id'])) {
                $ad = Ad::find($modifications['model_id']);
                if ($ad && $ad->status && $ad->status->value == 'draft') {
                    return false;
                }
            }
            return isset($modifications['collection_name']) && $modifications['collection_name'] == 'ads' ? true : false;
        }
        return false;
    }

    public function modification()
    {
        return $this->morphOne(Modification::class, 'modifiable');
    }
}
