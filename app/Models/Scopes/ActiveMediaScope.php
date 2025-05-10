<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveMediaScope implements Scope
{
    /**'
     *
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->orWhere(function ($query) {
            $query->where(function ($query) {
                $query->where('model_type', 'App\Models\Ad')
                    // ->where(function ($query) {
                 ->whereJsonDoesntContainKey('custom_properties->active')->whereJsonContains('custom_properties->active', true);

                    // });
            });

        });
    }
}
