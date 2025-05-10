<?php

namespace App\Traits;

use App\Models\Subscription;

trait HasSubscriptions
{

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscriber');
    }

    public function getActiveSubscriptions()
    {
        return $this->subscriptions()->active()->whereDate('ends_at', '>=', today())->get();
    }
}
