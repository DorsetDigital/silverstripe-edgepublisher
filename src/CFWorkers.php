<?php
namespace DorsetDigital\EdgePublisher;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

class CFWorkers implements EdgePublisher
{

    use Configurable;
    use Injectable;

    public function savePage($url, $content)
    {
        // TODO: Implement savePage() method.
    }

    public function deletePage($url)
    {
        // TODO: Implement deletePage() method.
    }
}