<?php

namespace DorsetDigital\EdgePublisher\Client;

use Aws\Credentials\Credentials;
use Aws\DynamoDb\Marshaler;
use Aws\Sdk;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

class AWS implements EdgePublisher
{
    use Configurable;
    use Injectable;

    private $sdk;
    private $db;

    /**
     * @var string
     * @config
     */
    private static $table_name;

    /**
     * @var string
     * @config
     */
    private static $url_field;

    /**
     * @var string
     * @config
     */
    private static $aws_region;

    /**
     * @var string
     * @config
     */
    private static $account_key;

    /**
     * @var string
     * @config
     */
    private static $account_secret;


    /**
     * AWS constructor.
     */
    public function __construct()
    {
        $key = $this->config()->get('account_key');
        $secret = $this->config()->get('account_secret');
        $region = $this->config()->get('aws_region');

        $credentials = new Credentials($key, $secret);

        $this->sdk = new Sdk([
            'region' => $region,
            'version' => 'latest',
            'credentials' => $credentials
        ]);
        $this->db = $this->sdk->createDynamoDb();
    }

    /**
     * Adds a page to the database
     * @param $url
     * @param $content
     */
    public function savePage($url, $content)
    {
        $mashaler = new Marshaler();
        $data = [
            $this->config()->get('url_field') => $url,
            'content' => $content
        ];
        $item = $mashaler->marshalJson(json_encode($data));

        return $this->db->putItem([
            'TableName' => $this->config()->get('table_name'),
            'Item' => $item
        ]);
    }

    /**
     * Removes a page from the database
     * @param $url
     */
    public function deletePage($url)
    {
        $params = [
            'TableName' => $this->config()->get('table_name'),
            'Key' => $url
        ];
        return $this->db->deleteItem($params);
    }
}
