<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Pollen\WpDatabase\Eloquent\Scopes\TaxonomyScope;

/**
 * @property-read int $term_taxonomy_id
 * @property int $term_id
 * @property string $taxonomy
 * @property string $description
 * @property int $parent
 * @property int $count
 * @property Term $term
 */
class TermTaxonomy extends AbstractModel
{
    /**
     * One or more taxonomy scope constraint.
     * @var string|string[]
     */
    public $taxonomyScope = '';

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->primaryKey = 'term_taxonomy_id';
        $this->table = 'term_taxonomy';
        $this->timestamps = false;

        $this->casts = array_merge(
            [
                'term_taxonomy_id' => 'integer',
                'term_id'          => 'integer',
                'taxonomy'         => 'string',
                'description'      => 'string',
                'parent'           => 'integer',
                'count'            => 'integer',
            ],
            $this->casts
        );

        parent::__construct($attributes);
    }

    /**
     * @inheritDoc
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TaxonomyScope());
    }

    /**
     * @return HasOne
     */
    public function term(): HasOne
    {
        return $this->hasOne(Term::class, 'term_id', 'term_id');
    }
}
