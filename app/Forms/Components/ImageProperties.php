<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Livewire\Attributes\On;

class ImageProperties extends Field
{
    protected string $view = 'forms.components.image-properties';

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerListeners([
            'remove-image-property' => [
                function (Field $component, $key) {
                    $state = $component->getState();
                    if (isset($state[$key])) {
                        unset($state[$key]);
                        $component->state($state);
                    }
                },
            ],
        ]);
    }
}
