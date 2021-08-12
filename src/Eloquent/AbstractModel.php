<?php

declare(strict_types=1);

namespace Pollen\WpDatabase\Eloquent;

use Pollen\Database\Eloquent\AbstractModel as BaseAbstractModel;
use Pollen\WpDatabase\WpDatabaseProxy;

abstract class AbstractModel extends BaseAbstractModel
{
    use WpDatabaseProxy;
}