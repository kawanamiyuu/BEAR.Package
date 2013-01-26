<?php

namespace BEAR\Package\tests\Module\Database\Dbal;

use Pagerfanta\Pagerfanta;
use BEAR\Package\Module\Database\Dbal\Pager;
use BEAR\Package\Module\Database\Dbal\PagerfantaDbalAdapter;
use Doctrine\DBAL\DriverManager;

/**
 * Test class for Pager.
 */
class PagerTest extends \PHPUnit_Extensions_Database_TestCase
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
        $this->query = 'SELECT * FROM posts';
        $this->pager = new Pager($db, new Pagerfanta(new PagerfantaDbalAdapter($db, $this->query)));
    }

    public function test_getPagerQuery()
    {
        $query = $this->pager->getPagerQuery($this->query);
        $expected = 'SELECT * FROM posts LIMIT 10 OFFSET 0';
        $this->assertSame($expected, $query);
    }
}
