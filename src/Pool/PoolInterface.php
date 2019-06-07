<?php
declare(strict_types = 1);

namespace SixDreams\Pool;

/**
 * Basic Pool interface.
 */
interface PoolInterface
{
    /**
     * Execute task.
     *
     * @param array $args
     */
    public function submit(array $args): void;

    /**
     * Collect references to completed tasks. Returns false until pool have active jobs.
     *
     * @return bool
     */
    public function collect(): bool;
}
