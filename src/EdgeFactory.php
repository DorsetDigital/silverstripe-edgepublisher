<?php

namespace DorsetDigital\EdgePublisher;

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

            default:
                throw new \Exception(_t(__CLASS__.'.noclient', 'No valid edge client defined'));
        }
    }
}