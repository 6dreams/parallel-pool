<?php
declare(strict_types = 1);

namespace SixDreams\ThreadConfiguration;

use parallel\Sync as Atomic;

/**
 * Default thread configuration.
 */
class ThreadConfig implements ThreadConfigInterface
{
    /** @var Atomic|null */
    protected $flag;

    /** @var int */
    protected $id;

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
