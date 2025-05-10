<?php

namespace App\Livewire\User;

use App\Models\Page;
use Livewire\Attributes\Url;
use Livewire\Component;
use Parsedown;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

/**
 * PageDetail Component.
 * Displays the details of a specific page, converting Markdown content to HTML.
 */
class PageDetail extends Component
{
    use SEOToolsTrait;

    // Represents the Page model instance.
    public Page $page;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    /**
     * Mount lifecycle hook.
     * Converts the Markdown content of the page to HTML.
     */
    public function mount()
    {

        //If page is not visible then abort
        if(!$this->page->isVisible()){
            abort(404);
        }

        $this->convertContentToHtml();
        $this->setSeoData();
    }

    /**
     * Converts the Markdown content of the page to HTML.
     */
    protected function convertContentToHtml()
    {
        $parsedown = new Parsedown();
        $this->page->content = $parsedown->text($this->page->content);
    }

    /**
     * Renders the PageDetail view.
     */
    public function render()
    {
        return view('livewire.user.page-detail');
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

        $title = $this->page->seo_title . " $separator " . $siteName;
        $description = $this->page->seo_description;
        $ogImage = getSettingMediaUrl('seo.ogimage', 'seo', asset('images/ogimage.jpg'));
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
