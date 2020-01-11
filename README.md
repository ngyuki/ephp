# ephp

**ephp** is pure plain php template with support auto escape.

## Example

```html
<!-- index.phtml -->
<strong><?= strtoupper($name) ?></strong>
```

Use `StreamFilter` (for development).

```php
<?php
$filter = new StreamFilter();

(function ($filename, $variables) use ($filter) {
    extract($variables, EXTR_SKIP);
    require $filter->path($filename); //=> <strong>A &amp; B</strong>
})('index.phtml', ['name' => 'a & b']);
```

Use `Compiler` (for production).

```php
<?php
$sourceDir = __DIR__;
$compiledDir = __DIR__ . '/compiled';
$compiler = new Compiler($sourceDir, $compiledDir);

(function ($filename, $variables) use ($compiler) {
    extract($variables, EXTR_SKIP);
    require $compiler->compile($filename); //=> <strong>A &amp; B</strong>
})('index.phtml', ['name' => 'a & b']);
```
