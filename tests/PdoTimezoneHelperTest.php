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
 * @since     30.09.2016
 * @link      http://github.com/heiglandreas/org.heigl.PdoTimezoneHelper
 */

namespace Org_Heigl\PdoTimezoneHelperTest;

use Org_Heigl\PdoTimezoneHelper\Exception\UnsupportedDatabaseDriverException;
use Org_Heigl\PdoTimezoneHelper\Handler\MysqlHandler;
use Org_Heigl\PdoTimezoneHelper\Handler\PdoTimezoneHandlerInterface;
use Org_Heigl\PdoTimezoneHelper\Handler\PostgresqlHandler;
use Org_Heigl\PdoTimezoneHelper\Handler\SqliteHandler;
use Org_Heigl\PdoTimezoneHelper\PdoTimezoneHelper;
use PDO;

class PdoTimezoneHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider creationOfHelperReturnsCorrectHandlerProvider */
    public function testThatCreationOfHelperReturnsCorrectHandler($pdo, $expectedHAndler)
    {
        $handler = @PdoTimezoneHelper::create($pdo);
        $this->assertInstanceOf($expectedHAndler, $handler);
    }

    public function creationOfHelperReturnsCorrectHandlerProvider()
    {
        return [
            'sqlite' => [
                new PDO('sqlite::memory:'),
                SqliteHandler::class],
            'mysql'  => [
                new PDO($GLOBALS['MYSQL_DB_DSN'], $GLOBALS['MYSQL_DB_USER'], $GLOBALS['MYSQL_DB_PASSWD']),
                MysqlHandler::class
            ],
            'pgsql'  => [
                new PDO($GLOBALS['POSTGRES_DB_DSN'], $GLOBALS['POSTGRES_DB_USER'], $GLOBALS['POSTGRES_DB_PASSWD']),
                PostgresqlHandler::class
            ],
        ];
    }

    public function testThatCreationOfAnSqlitehandlerRaisesWarning()
    {
        try {
            PdoTimezoneHelper::create(new PDO('sqlite::memory:'));
        } catch (\Exception $e) {
            $this->assertInstanceOf('\PHPUnit_Framework_Error_Warning', $e);
            $this->assertEquals('Using the Sqlite-Handler will result in unexpected behaviours as there is no server-sided Timezone-handling! Use at your own risk!', $e->getMessage());
        }

    }

    public function testThatCreationOfAnUnknownHandlerRaisesAnException()
    {
        $pdo = $this->getMockBuilder('\PDO')->disableOriginalConstructor()->getMock();
        $pdo->method('getAttribute')->willReturn('test');
        try {
            PdoTimezoneHelper::create($pdo);
        } catch (UnsupportedDatabaseDriverException $e) {
            $this->assertInstanceOf(
                UnsupportedDatabaseDriverException::class,
                $e
            );
            $this->assertEquals('Unsupported Database-Driver "test" detected.', $e->getMessage());
            return;
        }

        $this->fail('AssertionExpected');
    }
}
