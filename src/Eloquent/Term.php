<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $term_id
 * @property-read string $name
 * @property string $slug
 * @property int term_group
 * @property Collection $metas
 * @property TermTaxonomy $taxonomy
 */
class Term extends AbstractModel
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->primaryKey = 'term_id';
        $this->table = 'terms';
        $this->timestamps = false;

        $this->casts = array_merge(
            [
                'term_id'    => 'integer',
                'name'       => 'string',
                'slug'       => 'string',
                'term_group' => 'integer',
            ],
            $this->casts
        );

        parent::__construct($attributes);
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany(TermMeta::class, 'term_id');
    }

    /**
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(TermTaxonomy::class, 'term_id', 'term_id');
    }
}