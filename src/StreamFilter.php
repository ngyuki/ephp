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
        return "php://filter/read=ephp.{$this->echo}/resource={$filename}";
    }
}
