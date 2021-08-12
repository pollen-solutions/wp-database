<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pollen\Database\Eloquent\Casts\SerializedCast;

/**
 * @property-read int $meta_id
 * @property int $post_id
 * @property string $meta_key
 * @property mixed $meta_value
 * @property User $user
 */
class UserMeta extends AbstractModel
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->connection = $this->wpDb()->mainConnexion();
        $this->primaryKey = 'umeta_id';
        $this->table = 'usermeta';
        $this->timestamps = false;

        $this->casts = array_merge(
            [
                'meta_id'    => 'integer',
                'user_id'    => 'integer',
                'meta_key'   => 'string',
                'meta_value' => SerializedCast::class,
            ],
            $this->casts
        );

        parent::__construct($attributes);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
}