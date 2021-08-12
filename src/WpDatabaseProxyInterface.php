<?php

declare(strict_types=1);

namespace Pollen\WpDatabase;

interface WpDatabaseProxyInterface
{
    /**
     * Retrieves WordPress database instance.
     *
     * @return WpDatabaseProxyInterface
     */
    public function wpDb(): WpDatabaseProxyInterface;

    /**
     * Sets WordPress database instance.
     *
     * @param WpDatabaseProxyInterface $wpDatabase
     *
     * @return void
     */
    public function setWpDb(WpDatabaseProxyInterface $wpDatabase): void;
}