<?php

declare(strict_types=1);

namespace Pollen\WpDatabase;

use InvalidArgumentException;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Env;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\DbProxy;
use Psr\Container\ContainerInterface as Container;

class WpDatabase implements WpDatabaseInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ContainerProxy;
    use DbProxy;

    /**
     * Wordpress database main instance.
     * @var WpDatabaseInterface|null
     */
    private static ?WpDatabaseInterface $instance = null;

    /**
     * @var string|null
     */
    protected ?string $basePrefix = null;

    /**
     * @var int[]|null
     */
    protected ?array $blogIds = null;

    /**
     * @var string|null
     */
    protected ?string $charset = null;

    /**
     * @var string|null
     */
    protected ?string $collation = null;

    /**
     * @var string|null
     */
    protected ?string $database = null;

    /**
     * @var string|null
     */
    protected ?string $driver = null;

    /**
     * @var string|null
     */
    protected ?string $host = null;

    /**
     * @var string|null
     */
    protected ?string $password = null;

    /**
     * @var string|null
     */
    protected ?string $username = null;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Retrieve WordPress database instance.
     *
     * @return static
     */
    public static function getInstance(): WpDatabaseInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): WpDatabaseInterface
    {
        if (!$this->isBooted()) {
            $defaultConnection = [
                'driver'    => $this->getDriver(),
                'host'      => $this->getHost(),
                'database'  => $this->getDatabase(),
                'username'  => $this->getUsername(),
                'password'  => $this->getPassword(),
                'charset'   => $this->getCharset(),
                'collation' => $this->getCollation(),
                'prefix'    => $this->getBasePrefix(),
            ];

            try {
                $this->db()->getConnection();
            } catch (InvalidArgumentException $e) {
                $this->db()->addConnection($defaultConnection);
                $this->db()->setAsGlobal();
            }

            $this->db()->bootEloquent();

            $this->db()->addConnection($defaultConnection, $this->mainConnexion());

            foreach ($this->blogIds() as $id) {
                $this->db()->addConnection(
                    array_merge(
                        $defaultConnection,
                        ['prefix' => $this->getBlogPrefix($id)]
                    ),
                    $this->blogConnection($id)
                );
            }

            if (is_multisite()) {
                $this->db()->getConnection()->setTablePrefix($this->getBlogPrefix());
            }

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function getBasePrefix(): string
    {
        if ($this->basePrefix === null) {
            if ($this->hasGlobalWpDb()) {
                global $wpdb;

                $this->basePrefix = $wpdb->base_prefix;
            } else {
                $this->basePrefix = Env::get('DB_PREFIX', 'wp_');
            }
        }
        return $this->basePrefix;
    }

    /**
     * @param int|null $id
     *
     * @return string
     */
    protected function getBlogPrefix(?int $id = null): string
    {
        if ($id === null) {
            $id = get_current_blog_id();
        }

        if (0 === $id || 1 === $id) {
            return $this->getBasePrefix();
        }
        return $this->getBasePrefix() . "{$id}_";
    }

    /**
     * @return string
     */
    protected function getCharset(): string
    {
        if ($this->charset === null) {
            $this->charset = defined('DB_CHARSET') ? DB_CHARSET : Env::get('DB_CHARSET', 'utf8mb4');
        }
        return $this->charset;
    }

    /**
     * @return string
     */
    protected function getCollation(): string
    {
        if ($this->collation === null) {
            $this->collation = defined('DB_COLLATE') ? DB_COLLATE : Env::get('DB_CHARSET', 'utf8mb4_unicode_ci');
        }
        return $this->collation;
    }

    /**
     * @return string|null
     */
    protected function getDatabase(): ?string
    {
        if ($this->database === null) {
            $this->database = defined('DB_NAME') ? DB_NAME : Env::get('DB_DATABASE');
        }
        return $this->database;
    }

    /**
     * @return string
     */
    protected function getDriver(): string
    {
        if ($this->driver === null) {
            $this->driver = Env::get('DB_DRIVER', 'mysql');
        }
        return $this->driver;
    }

    /**
     * @return string
     */
    protected function getHost(): string
    {
        if ($this->host === null) {
            $this->host = defined('DB_HOST') ? DB_HOST : Env::get('DB_HOST', '127.0.0.1');
        }
        return $this->host;
    }

    /**
     * @return string|null
     */
    protected function getPassword(): ?string
    {
        if ($this->password === null) {
            $this->password = defined('DB_PASSWORD') ? DB_PASSWORD : Env::get('DB_PASSWORD');
        }
        return $this->password;
    }

    /**
     * @return string|null
     */
    protected function getUsername(): ?string
    {
        if ($this->username === null) {
            $this->username = defined('DB_USER') ? DB_USER : Env::get('DB_USERNAME');
        }
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function blogConnection(int $id = 1): string
    {
        return "wp-database.blog.$id";
    }

    /**
     * @inheritDoc
     */
    public function blogIds(): array
    {
        if ($this->blogIds === null) {
            if ($this->db()->getConnection()->getSchemaBuilder()->hasTable('blogs')) {
                $this->blogIds = $this->db()->getConnection()->table('blogs')->get()->pluck('blog_id')->all();
            } else {
                $this->blogIds = [];
            }
        }
        return $this->blogIds;
    }

    /**
     * @inheritDoc
     */
    public function mainConnexion(): string
    {
        return "wp-database.main";
    }

    /**
     * @inheritDoc
     */
    public function hasGlobalWpDb(): bool
    {
        global $wpdb;

        return class_exists('wpdb') && is_a($wpdb, 'wpdb');
    }
}
