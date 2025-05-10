<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\LocaleSwitcher::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $isMainCategory = $this->getRecord()->isMainCategory();

        if ($isMainCategory) {
            return __('messages.t_ap_main_categories');
        }

        return __('messages.t_ap_subcategories');
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        $record = $this->getRecord();
        $isSubCategory = $record->isSubCategory();

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            ...$isSubCategory ? [route('filament.admin.resources.categories.edit', ['record' => $record->parent]) => $record->parent->name] : [],
            ...$isSubCategory ? [route('filament.admin.resources.categories.edit', ['record' => $record]) => $record->name] : [],
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        $adTypeId = $record->ad_type_id;

        // Update ads directly linked to the record
        $record->ads()->update(['ad_type_id' => $adTypeId]);

        // Iterate through subcategories and update ads
        $record->subcategories()->each(function ($category) use ($adTypeId) {
            // Update ads for the category itself
            $category->ads()->update(['ad_type_id' => $adTypeId]);

            // Update ads in child subcategories
            $category->subcategories()->each(function ($childCategory) use ($adTypeId) {
                $childCategory->ads()->update(['ad_type_id' => $adTypeId]);
            });
        });
    }
}
