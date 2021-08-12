<?php

declare(strict_types=1);

namespace Pollen\WpDatabase;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\DbProxyInterface;

interface WpDatabaseInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ContainerProxyInterface,
    DbProxyInterface
{
    /**
     * Booting.
     *
     * @return static
     */
    public function boot(): WpDatabaseInterface;

    /**
     * Gets database connexion identifier name for a blog of site network.
     *
     * @param int $id
     *
     * @return string
     */
    public function blogConnection(int $id = 1): string;

    /**
     * Gets the list of blog IDs for the site network.
     *
     * @return int[]|array
     */
    public function blogIds(): array;

    /**
     * Get the main connexion identifier name.
     *
     * @return string
     */
    public function mainConnexion(): string;

    /**
     * Checks if global wpdb object exists.
     *
     * @return bool
     */
    public function hasGlobalWpDb(): bool;
}
