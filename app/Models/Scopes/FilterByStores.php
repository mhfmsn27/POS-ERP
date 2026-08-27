<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FilterByStores implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user   = auth()->user();
        $store  = my_store();
        if ($user && $store != null) {
            if($user->role_type == 'user') {
                $builder->from($builder->getModel()->getTable())->where('' . $builder->getModel()->getTable() . '.store_id', $store);
            }
        }
    }
}
