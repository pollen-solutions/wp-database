<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Pollen\Support\Arr;
use Pollen\WpDatabase\Eloquent\Scopes\UserBlogScope;
use Pollen\WpDatabase\Eloquent\Scopes\UserRoleScope;

/**
 * @property-read int $ID
 * @property string $user_login
 * @property string $user_pass
 * @property string $user_nicename
 * @property string $user_email
 * @property string $user_url
 * @property Carbon $user_registered
 * @property string $user_activation_key
 * @property bool $user_status
 * @property string $display_name
 * @property bool $spam
 * @property bool $deleted
 * @property Collection $metas
 * @property Collection $posts
 *
 * @method Builder|static blog(int $blog)
 * @method Builder|static role(string|array $roles)
 */
class User extends AbstractModel
{
    /**
     * Network site blog ID scope constraint.
     * @var int|null $blogScope
     */
    public static ?int $blogScope = null;

    /**
     * One or more role scope constraint.
     * @var string|string[]
     */
    public $userRoleScope = '';

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->connection = $this->wpDb()->mainConnexion();
        $this->primaryKey = 'ID';
        $this->table = 'users';

        $this->casts = array_merge(
            [
                'ID'                  => 'integer',
                'user_login'          => 'string',
                'user_pass'           => 'string',
                'user_nicename'       => 'string',
                'user_email'          => 'string',
                'user_url'            => 'string',
                'user_registered'     => 'datetime',
                'user_activation_key' => 'string',
                'user_status'         => 'boolean',
                'display_name'        => 'string',
                'spam'                => 'boolean',
                'deleted'             => 'boolean',
            ],
            $this->casts
        );

        $this->appends = array_merge(
            static::metaAttributes(),
            $this->appends
        );

        parent::__construct($attributes);
    }

    /**
     * @inheritDoc
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserRoleScope());
        static::addGlobalScope(new UserBlogScope());
    }

    /**
     * @return array
     */
    public static function metaAttributes(): array
    {
        return [
            'admin_color',
            'comment_shortcuts',
            'community_events_location',
            'description',
            'firstname',
            'lastname',
            'locale',
            'nickname',
            'primary_blog',
            'rich_editing',
            'syntax_highlighting',
            'show_admin_bar_front',
            'show_welcome_panel',
            'source_domain',
            'role',
            'roles',
            'use_ssl',
        ];
    }

    /**
     * @return string|null
     */
    public function getCreatedAtColumn(): ?string
    {
        return 'user_registered';
    }

    /**
     * @return string|null
     */
    public function getUpdatedAtColumn(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getAdminColorAttribute(): string
    {
        return ($e = $this->metas->where('meta_key', 'admin_color')->first()) ? (string)$e->meta_value : '';
    }

    /**
     * @return bool
     */
    public function getCommentShortcutsAttribute(): bool
    {
        return (($e = $this->metas->where('meta_key', 'comment_shortcuts')->first())) && $e->meta_value;
    }

    /**
     * @return array
     */
    public function getCommunityEventsLocationAttribute(): array
    {
        return ($e = $this->metas->where('meta_key', 'community-events-location')->first(
        )) ? (array)$e->meta_value : [];
    }

    /**
     * @return string
     */
    public function getDescriptionAttribute(): string
    {
        return ($e = $this->metas->where('meta_key', 'description')->first()) ? (string)$e->meta_value : '';
    }

    /**
     * @return string
     */
    public function getFirstnameAttribute(): string
    {
        return ($e = $this->metas->where('meta_key', 'first_name')->first()) ? (string)$e->meta_value : '';
    }

    /**
     * @return string
     */
    public function getLastnameAttribute(): string
    {
        return ($e = $this->metas->where('meta_key', 'last_name')->first()) ? (string)$e->meta_value : '';
    }

    /**
     * @return string
     */
    public function getLocaleAttribute(): string
    {
        return ($e = $this->metas->where('meta_key', 'locale')->first()) ? (string)$e->meta_value : '';
    }

    /**
     * @return string
     */
    public function getNicknameAttribute(): string
    {
        return ($e = $this->metas->where('meta_key', 'nickname')->first()) ? (string)$e->meta_value : '';
    }

    /**
     * @return int
     */
    public function getPrimaryBlogAttribute(): int
    {
        return ($e = $this->metas->where('meta_key', 'primary_blog')->first()) ? (int)$e->meta_value : 0;
    }

    /**
     * @return bool
     */
    public function getRichEditingAttribute(): bool
    {
        return (($e = $this->metas->where('meta_key', 'rich_editing')->first())) && $e->meta_value;
    }

    /**
     * @return string
     */
    public function getRoleAttribute(): string
    {
        if ($roles = array_filter($this->getRolesAttribute())) {
            return key($roles);
        }
        return '';
    }

    /**
     * @return array
     */
    public function getRolesAttribute(): array
    {
        $roleKey = $this->getConnection()->getTablePrefix() . 'capabilities';

        return ($e = $this->metas->where('meta_key', $roleKey)->first()) ? (array)$e->meta_value : [];
    }

    /**
     * @return bool
     */
    public function getSyntaxHighlightingAttribute(): bool
    {
        return (($e = $this->metas->where('meta_key', 'syntax_highlighting')->first())) && $e->meta_value;
    }

    /**
     * @return bool
     */
    public function getShowAdminBarFrontAttribute(): bool
    {
        return (($e = $this->metas->where('meta_key', 'show_admin_bar_front')->first())) && $e->meta_value;
    }

    /**
     * @return bool
     */
    public function getShowWelcomePanelAttribute(): bool
    {
        return (($e = $this->metas->where('meta_key', 'show_welcome_panel')->first())) && $e->meta_value;
    }

    /**
     * @return string
     */
    public function getSourceDomainAttribute(): string
    {
        return ($e = $this->metas->where('meta_key', 'source_domain')->first()) ? (string)$e->meta_value : '';
    }

    /**
     * @return bool
     */
    public function getUseSslAttribute(): bool
    {
        return (($e = $this->metas->where('meta_key', 'use_ssl')->first())) && $e->meta_value;
    }

    /**
     * @return HasMany
     */
    public function metas(): HasMany
    {
        return $this->hasMany(UserMeta::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'post_author');
    }

    /**
     * Reset blog scope contraint.
     *
     * @return void
     */
    public static function resetBlogScope(): void
    {
        static::$blogScope = null;
    }

    /**
     * Sets blog scope contraint.
     *
     * @param int $blog
     *
     * @return void
     */
    public static function setBlogScope(int $blog): void
    {
        static::$blogScope = $blog;
    }

    /**
     * Limits the scope of the request to a blog of site network.
     *
     * @param Builder $query
     * @param int $blog
     *
     * @return Builder
     */
    public function scopeBlog(Builder $query, int $blog): Builder
    {
        $query->withoutGlobalScope(UserBlogScope::class);

        $prefix = $this->getConnection()->getTablePrefix();
        if (($blog !== 1) && ($blog !== 0)) {
            $prefix .= $blog . '_';
        }

        $query->whereHas('metas', function (Builder $builder) use ($prefix) {
            $builder->where('meta_key', "{$prefix}capabilities");
        });

        return $query;
    }

    /**
     * Limits the scope of the request to user role.
     *
     * @param Builder $query
     * @param string|array $roles
     *
     * @return Builder
     */
    public function scopeRole(Builder $query, $roles): Builder
    {
        if (is_string($roles)) {
            $roles = Arr::wrap($roles);
        }

        if (is_array($roles)) {
            $query->whereHas('metas', function (Builder $builder) use ($roles) {
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

        return $query;
    }
}
