<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Mokhosh\FilamentRating\Components\Rating;

class StarRating extends Rating
{
    protected string $view = 'forms.components.rating.component';

    public function getView(): string
    {
        return 'forms.components.rating.component';
    }
}
