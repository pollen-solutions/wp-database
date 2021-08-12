<?php

declare(strict_types=1);

namespace Pollen\WpDatabase;

use Pollen\Container\BootableServiceProvider;

class WpDatabaseServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        WpDatabaseInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            WpDatabaseInterface::class,
            function () {
                return new WpDatabase([], $this->getContainer());
            }
        );
    }
}
