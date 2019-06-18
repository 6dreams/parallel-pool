# Pools in php-parallel extension
Provides simmular pool interface in parallel as pthreads.

## Usage
```php
$pool = new Pool(
    __DIR__ . '/bootstrap_or_autoloader.php',
    2,
    static function (ThreadConfigInterface $config) {
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
```