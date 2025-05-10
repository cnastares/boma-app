<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;
enum FieldValidationType: string implements HasLabel
{

    case ALPHA = 'alpha';
    // case ALPHASPACE = 'alpha_space';
    case ALPHADASH = 'alpha_dash';
    // case ALPHADASHSPACE = 'alpha_dash_space';
    case ALPHANUMERIC = 'alpha_numeric';
    // case ALPHANUMERICSPACE = 'alpha_numeric_space';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALPHA => 'Alpha',
            self::ALPHADASH => 'Alpha Dash',
            // self::ALPHADASHSPACE => 'Alpha Dash with space',
            self::ALPHANUMERIC => 'Alpha Numeric',
            // self::ALPHANUMERICSPACE => 'Alpha Numeric with space',
            // self::ALPHASPACE => 'Alpha with space',
        };
    }

    public static function helperTexts(): array
    {
        return [
            self::ALPHA->value => 'Only alphabetic characters (a-z, A-Z) are allowed. Spaces, numbers, and special characters are not permitted.',
            self::ALPHADASH->value => 'Only contain letters, numbers, dashes, and underscores.',
            self::ALPHANUMERIC->value => 'Alphabetic characters and numbers are allowed.',
            // self::ALPHASPACE->value => 'may only contain letters and spaces.',
            // self::ALPHADASHSPACE->value => 'may only contain letters and spaces.',
            // self::ALPHANUMERICSPACE->value => 'may only contain letters and spaces.',
        ];
    }
}
