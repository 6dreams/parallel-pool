<?php
declare(strict_types = 1);

namespace SixDreams\ThreadConfiguration;

/**
 * Thread configuration interface, sended in first argument in {@see Pool}'s closure.
 */
interface ThreadConfigInterface
{
    /**
     * Returns thread id.
     *
     * @return int
     */
    public function getId(): int;
}
