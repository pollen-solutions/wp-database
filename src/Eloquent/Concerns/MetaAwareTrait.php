<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent\Concerns;

trait MetaAwareTrait
{
    /**
     * Gets the value of a metadata by its meta_key.
     *
     * @param string $meta_key
     * @param mixed $default
     * @param bool $isSingle
     *
     * @return mixed
     */
    public function getMeta(string $meta_key, $default = null, bool $isSingle = false)
    {
        $query = $this->metas()->where('meta_key', $meta_key);

        if ($isSingle === true) {
            if ($meta = $query->first()) {
                return $meta->getAttribute('meta_value');
            }
            return $default;
        }

        $metas = [];
        $collection = $query->get();
        foreach ($collection as $meta) {
            $metas[$meta->getKey()] = $meta->getAttribute('meta_value');
        }

        return !empty($metas) ? : $default;
    }

    /**
     * Gets the single value of metadata by its meta_key.
     *
     * @param string $meta_key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMetaSingle(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, $default, true);
    }

    /**
     * Gets the multi value of a metadata by its meta_key.
     *
     * @param string $meta_key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMetaMulti(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, $default);
    }
}
