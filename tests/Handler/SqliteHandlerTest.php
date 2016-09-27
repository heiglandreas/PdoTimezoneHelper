<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
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
 * @since     23.09.2016
 * @link      http://github.com/heiglandreas/PdoTimezoneHelper
 */

namespace Org_Heigl\PdoTimezoneHelperTest\Handler;

use Org_Heigl\PdoTimezoneHelper\Handler\SqliteHandler;

class SqliteHandlerTest extends \PHPUnit_Framework_TestCase
{

    public function testThatCreatingAnSqliteHanderRaisesAWarning()
    {
        try {
            $handler = new SqliteHandler();
        } catch (\Exception $e) {
            $this->assertInstanceOf('\PHPUnit_Framework_Error_Warning', $e);
            $this->assertEquals('Using the Sqlite-Handler will result in unexpected behaviours as there is no server-sided Timezone-handling! Use at your own risk!', $e->getMessage());
        }
    }

    public function testThatCallingGetUtcDateTimeRaisesAWarning()
    {
        $handler = @new SqliteHandler();
        try {
            $handler->getUtcDateTime('test', 'test');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\PHPUnit_Framework_Error_Warning', $e);
            $this->assertEquals('This will only work when you have a timezone-offset on your timestamp', $e->getMessage());
        }
    }

    public function testThatCalingGetUtcDateTimeReturnsExpectedValue()
    {
        $handler = @new SqliteHandler();

        $this->assertEquals('datetime(`foo`)' , @$handler->getUtcDateTime('foo', 'bar'));
    }

    public function testThatCallingGetUtcDateTimeReturnsDecentData()
    {
        $pdo = new \PDO('sqlite::memory:');
    }
}
