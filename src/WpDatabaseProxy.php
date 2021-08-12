<?php

declare(strict_types=1);

namespace Pollen\WpDatabase;

use Pollen\Support\ProxyResolver;
use RuntimeException;

/**
 * @see \Pollen\WpDatabase\WpDatabaseProxyInterface
 */
trait WpDatabaseProxy
{
    /**
     * Related WordPress database instance.
     * @var WpDatabaseInterface|null
     */
    private ?WpDatabaseInterface $wpDatabase = null;

    /**
     * Retrieves WordPress database instance.
     *
     * @return WpDatabaseInterface
     */
    public function wpDb(): WpDatabaseInterface
    {
        if ($this->wpDatabase === null) {
            try {
                $this->wpDatabase = WpDatabase::getInstance();
            } catch (RuntimeException $e) {
                $this->wpDatabase = ProxyResolver::getInstance(
                    WpDatabaseInterface::class,
                    WpDatabase::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }
        }

        return $this->wpDatabase;
    }

    /**
     * Sets WordPress database instance.
     *
     * @param WpDatabase $wpDatabase
     *
     * @return void
     */
    public function setWpDb(WpDatabase $wpDatabase): void
    {
        $this->wpDatabase = $wpDatabase;
    }
}