<?php

namespace App\Models;

use App\Observers\AdObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Foundation\AdBase\Models\BaseAdModel;

#[ObservedBy([AdObserver::class])]
class Ad extends BaseAdModel
{

}
