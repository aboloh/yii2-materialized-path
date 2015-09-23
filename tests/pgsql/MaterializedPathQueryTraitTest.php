<?php
/**
 * @link https://github.com/paulzi/yii2-materialized-path
 * @copyright Copyright (c) 2015 PaulZi <pavel.zimakoff@gmail.com>
 * @license MIT (https://github.com/paulzi/yii2-materialized-path/blob/master/LICENSE)
 */

namespace paulzi\materializedpath\tests\pgsql;

use paulzi\materializedpath\tests\MaterializedPathQueryTraitTestCase;

/**
 * @group pgsql
 *
 * @author PaulZi <pavel.zimakoff@gmail.com>
 */
class MaterializedPathQueryTraitTest extends MaterializedPathQueryTraitTestCase
{
    protected static $driverName = 'pgsql';
}