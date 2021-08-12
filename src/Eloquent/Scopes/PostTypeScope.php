<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Pollen\WpDatabase\Eloquent\Post;

class PostTypeScope implements Scope
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (($model instanceof Post) && ($post_type = $model->postTypeScope)) {
            if (is_array($post_type)) {
                $builder->whereIn('post_type', $post_type);
            } elseif (is_string($post_type)) {
                $builder->where('post_type', $post_type);
            }
        }
    }
}