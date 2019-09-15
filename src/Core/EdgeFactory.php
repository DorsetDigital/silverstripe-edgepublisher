<?php

namespace DorsetDigital\EdgePublisher\Core;

use DorsetDigital\EdgePublisher\Client\AWS;
use DorsetDigital\EdgePublisher\Client\Cloudflare;

class EdgeFactory
{
    /**
     * @param string $client
     * @return mixed
     * @throws \Exception
     */
    public static function buildFor($client)
    {
        switch (strtolower($client)) {
            case 'aws':
                return AWS::create();
            case 'cloudflare':
                return Cloudflare::create();

            default:
                throw new \Exception(_t(__CLASS__.'.noclient', 'No valid edge client defined'));
        }
    }
}