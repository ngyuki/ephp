<?php
namespace ngyuki\Ephp;

use php_user_filter;

/**
 * @property resource $stream
 * @property string $filtername
 */
class StreamFilter extends php_user_filter
{
    private static $registered = false;

    /**
     * @var string
     */
    private $source = '';

    public static function path(string $file, string $echo): string
    {
        if (self::$registered === false) {
            stream_filter_register('ephp.*', static::class);
            self::$registered = true;
        }
        return "php://filter/read=ephp.{$echo}/resource={$file}";
    }

    public function onCreate()
    {
        return true;
    }

    public function onClose()
    {
        // noop
    }

    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->source .= $bucket->data;
            $consumed += $bucket->datalen;
        }
        if ($closing) {
            $escape = substr(strrchr($this->filtername, '.'), 1);
            $compiler = new Compiler($escape);
            $bucket = stream_bucket_new($this->stream, $compiler->compile($this->source, null));
            stream_bucket_append($out, $bucket);
            $this->source = '';
        }
        return PSFS_PASS_ON;
    }
}
