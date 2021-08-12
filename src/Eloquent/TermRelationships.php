<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $object_id
 * @property int $term_taxonomy_id
 * @property int $term_order
 * @property Post $post
 * @property TermTaxonomy $taxonomy
 */
class TermRelationships extends AbstractModel
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->primaryKey = null;
        $this->incrementing = false;
        $this->table = 'term_relationships';
        $this->timestamps = false;

        $this->casts = array_merge(
            [
                'object_id'        => 'integer',
                'term_taxonomy_id' => 'integer',
                'term_order'       => 'integer',
            ],
            $this->casts
        );

        parent::__construct($attributes);
    }

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->BelongsTo(Post::class, 'object_id');
    }

    /**
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->BelongsTo(TermTaxonomy::class, 'term_taxonomy_id');
    }
}