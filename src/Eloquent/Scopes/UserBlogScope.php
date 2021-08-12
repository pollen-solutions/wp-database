<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Pollen\WpDatabase\Eloquent\User;

class UserBlogScope implements Scope
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (($model instanceof User) && ($model::$blogScope !== null)) {
            $prefix = $model->getConnection()->getTablePrefix();
            if (($model::$blogScope !== 1) && ($model::$blogScope !== 0)) {
                $prefix .= $model::$blogScope . '_';
            }

            $builder->whereHas(
                'metas',
                function (Builder $builder) use ($prefix) {
                    $builder->where('meta_key', "{$prefix}capabilities");
                }
            );
        }
    }
}