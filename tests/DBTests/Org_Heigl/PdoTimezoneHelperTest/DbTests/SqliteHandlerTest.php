<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     26.09.2016
 * @link      http://github.com/heiglandreas/org.heigl.PdoTimezoneHelper
 */

namespace Org_Heigl\PdoTimezoneHelperTest\DbTests;

use Org_Heigl\PdoTimezoneHelper\Handler\SqliteHandler;

class SqliteHandlerTest extends \PHPUnit_Extensions_Database_TestCase
{
    protected $pdo = null;

    /**
     * @return \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
     */
    public function getConnection()
    {
        if ($this->pdo === null) {
            $this->pdo = new \PDO('sqlite::memory:');
            $this->pdo->exec('CREATE TABLE `test` (id int, datetime datetime, timezone string)');
        }
        return $this->createDefaultDBConnection($this->pdo, ':memory:');
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__.'/_files/sqlite-timezone-seed.xml');
    }

    public function testDataBaseConnection()
    {
        $queryTable = $this->getConnection()->createQueryTable(
            'test', 'SELECT * FROM test'
        );
        $expectedTable = $this->getDataSet()->getTable('test');
        //Here we check that the table in the database matches the data in the XML file
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testThatRetrievingDateTimeReturnsUtcValues()
    {
        $handler = @new SqliteHandler();
        $this->getDataSet();
        $con = $this->getConnection()->createQueryTable(
            'test', sprintf(
                'SELECT %1$s as datetime FROM `test`',
                @$handler->getUtcDateTime('datetime', 'timezone')
            )
        );

        $this->assertEquals('2010-04-24 15:15:23', $con->getValue(0, 'datetime'));
        $this->assertEquals('2010-04-26 19:14:20', $con->getValue(1, 'datetime'));
    }
}
