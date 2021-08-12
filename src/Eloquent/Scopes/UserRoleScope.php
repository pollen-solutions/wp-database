<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Pollen\Support\Arr;
use Pollen\WpDatabase\Eloquent\User;

class UserRoleScope implements Scope
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (($model instanceof User) && ($roles = $model->userRoleScope)) {
            if (is_string($roles)) {
                $roles = Arr::wrap($roles);
            }

            if (is_array($roles)) {
                $builder->whereHas('metas', function (Builder $builder) use ($roles) {
                    $builder->where(function (Builder $builder) use ($roles) {
                        $or = false;
                        foreach ($roles as $role) {
                            $or ? $builder->orWhere('meta_value', 'LIKE', "%$role%")
                                : $builder->where('meta_value', 'LIKE', "%$role%");
                            $or = true;
                        }
                    });
                });
            }
        }
    }
}