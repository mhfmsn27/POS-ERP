<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FilterByMerchantBusiness implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user           = auth()->user();
        $merchantId     = $user != null ? $user->merchant_id : null;
        if ($user) {
            if ($user->role_type == 'user') {
                $builder->from($builder->getModel()->getTable())->where('' . $builder->getModel()->getTable() . '.merchant_business_id', $merchantId);
            }
        }
    }
}
