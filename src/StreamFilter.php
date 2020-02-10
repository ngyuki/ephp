<?php
namespace ngyuki\Ephp;

class StreamFilter
{
    private static $registered = false;

    private $echo;

    public function __construct(string $echo = 'htmlspecialchars')
    {
        $this->echo = $echo;

        if (self::$registered === false) {
            stream_filter_register('ephp.*', StreamFilterImpl::class);
            self::$registered = true;
        }
    }

    public function path(string $filename): string
    {
        // Apply read filter with php stream wrapper
        // @see https://github.com/php/php-src/blob/master/ext/standard/php_fopen_wrapper.c#L339-L381
        return "php://filter/read=ephp.{$this->echo}/resource={$filename}";
    }
}
