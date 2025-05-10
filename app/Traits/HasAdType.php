<?php

namespace App\Traits;

use App\Models\AdType;
use Carbon\Carbon;

trait HasAdType
{
    public function adType()
    {
        return $this->belongsTo(AdType::class, 'ad_type_id', 'id');
    }
}
