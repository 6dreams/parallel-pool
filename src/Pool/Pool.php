<?php
declare(strict_types = 1);

namespace SixDreams\Pool;

use parallel\Future;
use parallel\Runtime;
use SixDreams\ThreadConfiguration\ThreadConfig;
use SixDreams\ThreadConfiguration\ThreadConfigInterface;

/**
 * Lightweight replacement for php-pthreads Pool.
 */
class Pool implements PoolInterface
{
    /** @var Runtime[] */
    protected $runtimes = [];

    /** @var Future[][] */
    protected $futures = [];

    /** @var callable */
    protected $closure;

    /**
     * Constructor.
     *
     * @param string   $autoloader
     * @param int      $size
     * @param callable $threadFunction
     */
    public function __construct(string $autoloader, int $size, callable $threadFunction)
    {
        $this->closure = $threadFunction;
        for ($i = 0; $i < $size; $i++) {
            $this->runtimes[] = new Runtime($autoloader);
            $this->futures[]  = [];
        }
    }

    /**
     * @inheritdoc
     */
    public function submit(array $args): void
    {
        foreach ($this->runtimes as $id => $runtime) {
            if ($this->isFinished($this->futures[$id])) {
                $this->run($id, $runtime, $args);

                return;
            }
        }

        $this->run(0, $this->runtimes[0], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): bool
    {
        foreach ($this->futures as &$future) {
            if (!$this->isFinished($future)) {
                return true;
            }
        }

        return false;
    }

    /**
     * You can override this method to create custom configuration.
     *
     * @param int $id
     *
     * @return ThreadConfigInterface
     */
    protected function createThreadConfig(int $id): ThreadConfigInterface
    {
        return new ThreadConfig($id);
    }

    /**
     * Internal. Checks thread finish all jobs and remove them for queue.
     *
     * @param Future[]|null $futures
     *
     * @return bool
     */
    private function isFinished(array &$futures): bool
    {
        foreach ($futures as $idx => $future) {
            if ($future->done() || $future->cancelled()) {
                unset($futures[$idx]);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Internal. Execute closure with arguments on selected runtime.
     *
     * @param int     $id
     * @param Runtime $runtime
     * @param array   $args
     */
    private function run(int $id, Runtime $runtime, array &$args): void
    {
        \array_unshift($args, $this->createThreadConfig($id));

        $this->futures[$id][] = $runtime->run($this->closure, $args);
    }
}
