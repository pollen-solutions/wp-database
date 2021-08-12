<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Pollen\WpDatabase\Eloquent\TermTaxonomy;

class TaxonomyScope implements Scope
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (($model instanceof TermTaxonomy) && ($taxonomy = $model->taxonomyScope)) {
            if (is_array($taxonomy)) {
                $builder->whereIn('taxonomy', $taxonomy);
            } elseif (is_string($taxonomy)) {
                $builder->where('taxonomy', $taxonomy);
            }
        }
    }
}