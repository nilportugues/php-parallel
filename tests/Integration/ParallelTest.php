<?php
/**
 * This file is part of RedisClient.
 * git: https://github.com/cheprasov/php-parallel
 *
 * (C) Alexander Cheprasov <cheprasov.84@ya.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Integration;
use Parallel\Parallel;
use Parallel\Storage\ApcuStorage;
use Parallel\Storage\MemcachedStorage;

/**
 * @see \Parallel\Parallel
 */
class ParallelTest extends \PHPUnit_Framework_TestCase {

    public function test_viaApcuStorage() {
        $Parallel = new Parallel(new ApcuStorage());

        $time = microtime(true);
        for ($i = 1; $i <= 5; ++$i) {
            $Parallel->run('n:'. $i, function() use ($i) {
                sleep($i);
                return $i * $i;
            });
        }
        sleep(4);
        $result = $Parallel->wait(['n:1', 'n:2', 'n:3', 'n:4', 'n:5']);
        $time = microtime(true) - $time;
        $this->assertGreaterThanOrEqual(5, $time);
        $this->assertLessThan(6, $time);

        $this->assertSame(['n:1' => 1, 'n:2' => 4, 'n:3' => 9, 'n:4' => 16, 'n:5' => 25], $result);
    }

    public function test_viaMemcachedStorage() {
        $Parallel = new Parallel(new MemcachedStorage(['servers'=>[['127.0.0.1', 11211]]]));

        $time = microtime(true);
        for ($i = 1; $i <= 5; ++$i) {
            $Parallel->run('n:'. $i, function() use ($i) {
                sleep($i);
                return $i * $i;
            });
        }
        sleep(4);
        $result = $Parallel->wait(['n:1', 'n:2', 'n:3', 'n:4', 'n:5']);
        $time = microtime(true) - $time;
        $this->assertGreaterThanOrEqual(5, $time);
        $this->assertLessThan(6, $time);

        $this->assertSame(['n:1' => 1, 'n:2' => 4, 'n:3' => 9, 'n:4' => 16, 'n:5' => 25], $result);
    }

}
