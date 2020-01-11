<?php
namespace ngyuki\Ephp;

use php_user_filter;

/**
 * @property resource $stream
 * @property string $filtername
 *
 * @internal
 */
class StreamFilterImpl extends php_user_filter
{
    /**
     * @var string
     */
    private $source = '';

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
            $echo = substr(strrchr($this->filtername, '.'), 1);
            $compiler = new Translator($echo, function ($expr) use ($echo) {
                return var_export("php://filter/read=ephp.{$echo}/resource=", true) . '.' . $expr;
            });
            $bucket = stream_bucket_new($this->stream, $compiler->translate($this->source, null));
            assert(is_object($bucket));
            stream_bucket_append($out, $bucket);
            $this->source = '';
        }
        return PSFS_PASS_ON;
    }
}
