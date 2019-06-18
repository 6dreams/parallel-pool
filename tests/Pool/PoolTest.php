<?php
declare(strict_types = 1);

namespace SixDreams\Tests\Pool;

use SixDreams\Pool\Pool;
use SixDreams\Tests\Data\Kernel;
use SixDreams\ThreadConfiguration\ThreadConfigInterface;
use PHPUnit\Framework\TestCase;

/**
 * Example.
 */
class PoolTest extends TestCase
{
    /**
     * Not real test.
     */
    public function testPool(): void
    {
        $pool = new Pool(
            __DIR__ . '/../test_thread_bootstrap.php',
            2,
            static function (ThreadConfigInterface $config) {
                Kernel::getInstance();

                echo $config->getId() . "\n";
            }
        );

        $pool->submit([]);
        $pool->submit([]);
        $pool->submit([]);
        $pool->submit([]);
        $pool->submit([]);

        while ($pool->collect()) {
            \usleep(0);
        }

        self::assertTrue(true);
    }
}
