<?php

namespace App\Livewire\Ad;

use App\Models\Ad;
use Livewire\Component;
use Illuminate\Support\Facades\Cookie;
use App\Models\Category;


class VerifyAge extends Component
{

    public $categorySlug;
    public $subCategorySlug;
    public $childCategorySlug;
    public $previousUrl;
    public $ageValue;
    public $canRepeat;


    /**
     * Checks if the user's age verification is required for the current category.
     * If age verification data is available and the user has not been verified,
     * dispatches an event to show the age verification popup.
     * Checks if any of the given categories require age verification.
     *
     * @param array $categorySlugs An array of category slugs to check.
     * @return array|null An associative array with 'id' and 'age_value' of the
     *                    first matching category, or null if no match is found.
     */
    public function checkAgeVerify()
    {
        $categorySlugs = [$this->categorySlug, $this->subCategorySlug, $this->childCategorySlug];
        $categories = Category::whereIn('slug', $categorySlugs ?? [])
            ->where('enable_age_verify', true)
            ->orderBy('id','asc')
            ->select('id', 'age_value')->get()->toArray(); // Convert to an array or return null if no match

            foreach ($categories as $category) {

            if ((!$this->canRepeat) && Cookie::has("age_verified_{$category['id']}")) {
                return;
            }
            if ($category != null && (!Cookie::has("age_verified_{$category['id']}"))) {

                //dd($category , Cookie::has("age_verified_{$category['id']}"));

                $this->ageValue = $category['age_value'];
                $this->dispatch('show-verify-age-popup', categoryId: $category['id']);
                 if(!$this->canRepeat)
                 {
                    return;
                 }
            }

        }
    }

    /**
     * Verifies the age for a given category by setting a cookie.
     *
     * @param int $categoryId The ID of the category to verify age for.
     * @return void
     */
    public function ageVerified($categoryId)
    {
        Cookie::queue("age_verified_{$categoryId}", true, 43200);
    }

    /**
     * Redirects the user to the previous URL.
     *
     * This method utilizes the redirect helper to navigate
     * back to the last visited page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectPreviousUrl()
    {
        return redirect()->to($this->previousUrl);
    }

    public function render()
    {
        return view("livewire.ad.verify-age");
    }
}


