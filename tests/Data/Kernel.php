<?php
declare(strict_types = 1);

namespace SixDreams\Tests\Data;

/**
 * Class Kernel
 */
class Kernel
{
    /** @var Kernel */
    private static $self;

    /**
     * Get instance and log info to console.
     *
     * @return Kernel
     */
    public static function getInstance(): Kernel
    {
        if (!static::$self) {
            echo "boot\n";
            static::$self       = new self();
        }

        return static::$self;
    }


}
