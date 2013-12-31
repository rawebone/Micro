# Micro

This is a framework for creating web applications in a professional, no-thrills
way with the goal of creating testable, easily maintainable applications while
being capable of meeting your requirements without adding bloat.

*Note: This is a work in progress*

## Example

```php

// public/index.php

require_once "../vendor/autoload.php";

$env = new \Micro\Util\DumbEnvironment();
$env->init();

$app = new \Micro\Application($env);
$app->attach(new MyController());
$app->run();
$app->send();

```
