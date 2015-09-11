<?php

namespace tests;

use tests\migrations\TestMigration;
use tests\models\AttributeModeNode;
use tests\models\MultipleTreeNode;
use tests\models\Node;
use Yii;
use yii\db\Connection;

class BaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    protected static $driverName = 'sqlite';


    /**
     * @inheritdoc
     */
    public function getConnection()
    {
        return $this->createDefaultDBConnection(Yii::$app->getDb()->pdo);
    }

    /**
     * @inheritdoc
     */
    public function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet(require(__DIR__ . '/data/data.php'));
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (Yii::$app->get('db', false) === null) {
            $this->markTestSkipped();
        } else {
            (new TestMigration())->up();
            if (Yii::$app->db->driverName === 'pgsql') {
                Yii::$app->db->createCommand("SELECT setval(pg_get_serial_sequence('tree', 'id'), 26)")->execute();
                Yii::$app->db->createCommand("SELECT setval(pg_get_serial_sequence('attribute_mode_tree', 'id'), 26)")->execute();
                Yii::$app->db->createCommand("SELECT setval(pg_get_serial_sequence('multiple_tree', 'id'), 26)")->execute();
            }
            parent::setUp();
        }
    }




    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        $config = require(__DIR__ . '/data/config.php');
        $config = $config[static::$driverName];
        $config['class'] = Connection::className();
        try {
            Yii::$app->set('db', $config);
            Yii::$app->getDb()->open();
        } catch (\Exception $e) {
            Yii::$app->clear('db');
        }
    }
}