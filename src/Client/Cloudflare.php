<?php
namespace DorsetDigital\EdgePublisher\Client;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

class Cloudflare implements EdgePublisher
{

    use Configurable;
    use Injectable;

    /**
     * @var string
     * @config
     */
    private static $cf_email;

    /**
     * @var string
     * @config
     */
    private static $auth_key;

    public function savePage($url, $content)
    {
        // TODO: Implement savePage() method.
    }

    public function deletePage($url)
    {
        // TODO: Implement deletePage() method.
    }
}