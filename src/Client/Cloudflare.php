<?php

namespace DorsetDigital\EdgePublisher\Client;

use GuzzleHttp\Client;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\SiteConfig\SiteConfig;
use DorsetDigital\EdgePublisher\Core\EdgePublisher;

class Cloudflare implements EdgePublisher
{

    use Configurable;
    use Injectable;

    const CF_API_BASE_URL = 'https://api.cloudflare.com/client/v4/accounts/';
    const CF_API_NS_ACTION = '/storage/kv/namespaces';
    const CF_API_KV_ACTION = '/values/';


    /**
     * Cloudflare email address
     *
     * @var string
     * @config
     */
    private static $cf_email;

    /**
     * Cloudflare auth key
     *
     * @var string
     * @config
     */
    private static $auth_key;

    /**
     * Cloudflare account ID
     *
     * @var string
     */
    private static $account_id;

    /**
     * Cloudflare KV namespace
     *
     * @var string
     * @config
     */
    private static $namespace_name;


    public function __construct()
    {
        //Sanity check our config - we must have email, auth key, namespace and account id to be able to perform a request
        $config = $this->config();
        if (
            empty($config->get('cf_email')) ||
            empty($config->get('auth_key')) ||
            empty($config->get('account_id')) ||
            empty($config->get('namespace_name'))
        ) {
            throw new \Exception(_t(__CLASS__ . '.ConstructError',
                "The publisher module does not appear to be correctly configured.  Please check."));
        }
    }


    /**
     * Save the page to the KV store
     * @param $url
     * @param $content
     */
    public function savePage($url, $content)
    {
        return $this->putPage($url, $content);
    }

    /**
     * Sends an empty page to the KV store with a 60 second expiry.
     * This has the effect of removing the page from the data store
     * @param $url
     */
    public function deletePage($url)
    {
        return $this->putPage($url, '', 60);
    }

    /**
     * Send the page to Cloudflare
     *
     * @param $slug
     * @param $body
     * @param int $expiry
     */
    private function putPage($slug, $body, $expiry = 0)
    {
        $nsID = SiteConfig::current_site_config()->CloudflareNamespaceID;
        if ($nsID == '') {
            throw new \Exception((_t(__CLASS__ . '.NSIDError',
                "Cannot run without a valid Namespace ID.  Please run the build process")));
        }

        $uri = Controller::join_links([
            self::CF_API_BASE_URL,
            $this->config()->get('account_id'),
            self::CF_API_NS_ACTION,
            $nsID,
            self::CF_API_KV_ACTION,
            $slug
        ]);

        $headers = [
            'x-auth-email' => $this->config()->get('cf_email'),
            'x-auth-key' => $this->config()->get('auth_key'),
            'content-type' => 'text/plain'
        ];

        $clientOpts = [
            'headers' => $headers
        ];

        if ($expiry > 0) {
            $clientOpts['query'] = [
                'expiration_ttl' => $expiry
            ];
        }

        $client = new Client($clientOpts);
        $response = $client->request('PUT', $uri, ['body' => $body]);
        if (floor($response->getStatusCode() / 100) === 2) {
            return true;
        }
        return false;

    }
}
