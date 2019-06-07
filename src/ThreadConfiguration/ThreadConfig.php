<?php
declare(strict_types = 1);

namespace SixDreams\ThreadConfiguration;

use parallel\Sync as Atomic;

/**
 * Class ThreadConfiguration
 */
class ThreadConfig implements ThreadConfigInterface
{
    /** @var Atomic */
    private $flag;

    /** @var int */
    private $id;

    /**
     * Конструктор.
     *
     * @param int    $id
     * @param Atomic $flag
     */
    public function __construct(int $id, ?Atomic $flag = null)
    {
        $this->flag = $flag;
        $this->id   = $id;
    }

    /**
     * @inheritdoc
     */
    public function getId(): int
    {
        return $this->id;
    }
}
