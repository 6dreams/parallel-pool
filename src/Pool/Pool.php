<?php
declare(strict_types = 1);

namespace SixDreams\Pool;

use parallel\Runtime;
use SixDreams\Pool\DTO\ThreadContext;
use SixDreams\ThreadConfiguration\ThreadConfig;
use SixDreams\ThreadConfiguration\ThreadConfigInterface;

/**
 * Lightweight replacement for php-pthreads Pool.
 */
class Pool implements PoolInterface
{
    /** @var Runtime[] */
    protected $runtimes = [];

    /** @var ThreadContext[][] */
    protected $contexts = [];

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
            $this->contexts[] = [];
        }
    }

    /**
     * @inheritdoc
     */
    public function submit(array $args): void
    {
        foreach (\array_keys($this->runtimes) as $id) {
            if ($this->isFinished($this->contexts[$id])) {
                $this->run($id, $args);

                return;
            }
        }

        $this->run(0, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): bool
    {
        foreach ($this->contexts as &$future) {
            if (!$this->isFinished($future)) {
                return false;
            }
        }

        return true;
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
     * @param ThreadContext[]|null $contexts
     *
     * @return bool
     */
    private function isFinished(array &$contexts): bool
    {
        foreach ($contexts as $idx => $context) {
            if ($context->finished()) {
                unset($contexts[$idx]);
                if ($context->hasError()) {
                    $this->run($context->getIndex(), $context->getArgs());

                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Internal. Execute closure with arguments on selected runtime.
     *
     * @param int   $id
     * @param array $args
     */
    private function run(int $id, array $args): void
    {
        \array_unshift($args, $this->createThreadConfig($id));

        $this->contexts[$id][] = new ThreadContext(
            $id,
            $this->runtimes[$id]->run($this->closure, $args),
            $args
        );
    }
}
