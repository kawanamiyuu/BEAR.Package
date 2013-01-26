<?php

namespace BEAR\Package\tests\Module\Database\Dbal;

use BEAR\Package\Module\Database\Dbal\PagingQuery;
use PDO;
use Doctrine\DBAL\DriverManager;

/**
 * Test class for Pager.
 */
class PagingQueryTest extends \PHPUnit_Extensions_Database_TestCase
{
    private $pdo;

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        $this->pdo = require __DIR__ . '/scripts/db.php';;

        return $this->createDefaultDBConnection($this->pdo, 'mysql');
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createMySQLXMLDataSet(__DIR__.'/mock/pager_seed.xml');
    }

    protected function setUp()
    {
        parent::setUp();
        $params['pdo'] = $this->pdo;
        $db = DriverManager::getConnection($params);
        $this->sql = 'SELECT * FROM posts';
        $this->pager = new PagingQuery($db, $this->sql);
    }

    public function test_New()
    {
        $this->assertInstanceOf('BEAR\Package\Module\Database\Dbal\PagingQuery', $this->pager);
    }

    public function test_count()
    {
        $count = count($this->pager);
        $this->assertSame(5, (integer) $count);
    }

    public function test_getPagerSql()
    {
        $result = $this->pager->getPagerSql(0, 10);
        $expected = 'SELECT * FROM posts LIMIT 10 OFFSET 0';
        $this->assertSame($expected, $result);
    }

    public function est_getIterator()
    {
        $offset = 1;
        $length = 2;
        $this->pager->setOffsetLength($offset, $length);
        $result = $this->pager->getIterator();
        $this->assertSame(2, (integer) $result[0]['id']);
        $this->assertSame(3, (integer) $result[1]['id']);
        $this->assertSame(2, count($result));
    }
}
