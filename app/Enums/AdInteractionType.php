<?php

namespace App\Enums;

enum AdInteractionType: string
{
    case EXTERNALLINKCLICK = 'external_link_click';
    case CHATCONTACT = 'chat_contact';

    public function label(): string
    {
        return match ($this) {
            self::EXTERNALLINKCLICK => 'External link click',
            self::CHATCONTACT => 'Chat contact',
        };
    }

}
