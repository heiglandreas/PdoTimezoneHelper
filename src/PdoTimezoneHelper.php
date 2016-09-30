<?php
/**
 * Copyright Andreas Heigl<andreas@heigl.org>
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
 * @link      https://github.com/heiglandreas/PdoTimezoneHelper
 */

namespace Org_Heigl\PdoTimezoneHelper;

use Org_Heigl\PdoTimezoneHelper\Exception\InvalidInterfaceException;
use Org_Heigl\PdoTimezoneHelper\Exception\UnsupportedDatabaseDriverException;
use Org_Heigl\PdoTimezoneHelper\Handler\PdoTimezoneHandlerInterface;
use PDO;

class PdoTimezoneHelper
{
    protected static $supportedDrivers = [
        'mysql'    => \Org_Heigl\PdoTimezoneHelper\Handler\MysqlHandler::class,
        'pgsql' => \Org_Heigl\PdoTimezoneHelper\Handler\PostgresqlHandler::class,
        'sqlite' => \Org_Heigl\PdoTimezoneHelper\Handler\SqliteHandler::class,
    ];

    /**
     * @param PDO $pdo
     *
     * @throws UnsupportedDatabaseDriverException
     * @return PdoTimezoneHandlerInterface
     */
    public static function create(PDO $pdo)
    {
        $backend = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if (! array_key_Exists($backend, self::$supportedDrivers)) {
            throw new UnsupportedDatabaseDriverException($backend);
        }

        return new self::$supportedDrivers[$backend];
    }

    /**
     * Set your own driver.
     *
     * The driver has to implement the PdoTimezoneHandlerInterface. Otherwise
     * this method will throw an InvalidInterfaceException.
     *
     * @param string $driver
     * @param string $class
     *
     * @throws InvalidInterfaceException
     */
    public static function addSupportedDriver($driver, $class)
    {
        $instance = new $class;
        if (! $instance instanceof PdoTimezoneHandlerInterface) {
            throw new InvalidInterfaceException(sprintf(
                'The class"%s" does not implement the required interface "%s"',
                $class,
                PdoTimezoneHandlerInterface::class
            ));
        }
        self::$supportedDrivers[$driver] = $class;
    }
}
