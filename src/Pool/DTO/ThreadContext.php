<?php
declare(strict_types = 1);

namespace SixDreams\Pool\DTO;

use parallel\Future;

/**
 * Executed thread context.
 */
class ThreadContext
{
    /** @var int */
    private $index;

    /** @var Future */
    private $future;

    /** @var array */
    private $args;

    /**
     * Конструктор.
     *
     * @param int    $index
     * @param Future $future
     * @param array  $args
     */
    public function __construct(int $index, Future $future, array $args)
    {
        $this->index  = $index;
        $this->future = $future;
        $this->args   = $args;
    }

    /**
     * Does finished.
     *
     * @return bool
     */
    public function finished(): bool
    {
        return $this->future->done() || $this->future->cancelled();
    }

    /**
     * Does execution finished with error.
     *
     * @return \Throwable|null
     */
    public function getError(): ?\Throwable
    {
        if ($this->future->cancelled()) {
            return null;
        }

        try {
            $this->future->value();

            return null;
        } catch (\Throwable $e) {
            return $e;
        }
    }

    /**
     * Get index of runtime.
     *
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Get arguments from current run.
     *
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
