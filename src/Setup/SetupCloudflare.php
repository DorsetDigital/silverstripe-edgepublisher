<?php

namespace DorsetDigital\EdgePublisher\Setup;

use DorsetDigital\EdgePublisher\Client\Cloudflare;
use GuzzleHttp\Client;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;
use SilverStripe\SiteConfig\SiteConfig;

class SetupCloudflare extends BuildTask
{

    private static $segment = 'DorsetDigital-CFInit';

    protected $title = 'Set up Cloudflare workers KV namespace';
    protected $description = 'Sets up the workers namespace and stores the ID.  Please ensure the module is configured before running this task';

    /**
     * @param HTTPRequest $request
     * @return
     */
    public function run($request)
    {
        if ($this->canRun() === false) {
            throw new \Exception(_t(__CLASS__ . '.BuildError',
                "Cannot run this task.  Please check that the config is complete"));
        }
        $siteConfig = SiteConfig::current_site_config();
        if ($siteConfig->CloudflareNamespaceID != "") {
            echo "<p>" . _t(__CLASS__ . '.AlreadyRun',
                    "The namespace ID has already been stored.  Nothing to do") . "</p>";
        } else {
            $nsID = $this->storeNamespace();
            if ($nsID !== false) {
                $siteConfig->CloudflareNamespaceID = $nsID;
                $siteConfig->write();
                echo "<p>" . _t(__CLASS__ . '.RunDone',
                        "The namespace ID has been generated and stored successfully.") . "</p>";
            } else {
                echo "<p>" . _t(__CLASS__ . '.RunError',
                        "Warning! There was a problem obtaining the namespace ID.  Please check your configuration carefully.") . "</p>";
            }
        }
    }

    private function canRun()
    {
        $config = Cloudflare::config();
        if (
            empty($config->get('cf_email')) ||
            empty($config->get('auth_key')) ||
            empty($config->get('account_id')) ||
            empty($config->get('namespace_name'))
        ) {
            return false;
        }
        return true;
    }

    private function storeNamespace()
    {
        $config = Cloudflare::config();
        $namespaceName = $config->get('namespace_name');

        $bodyData = json_encode([
            'title' => $namespaceName
        ]);

        $headers = [
            'x-auth-email' => $config->get('cf_email'),
            'x-auth-key' => $config->get('auth_key'),
            'content-type' => 'application/json'
        ];

        $uri = Controller::join_links([
            Cloudflare::CF_API_BASE_URL,
            $config->get('account_id'),
            Cloudflare::CF_API_NS_ACTION
        ]);

        $client = new Client(['headers' => $headers]);
        $response = $client->request('PUT', $uri, ['body' => $bodyData]);

        $resCode = (int)$response->getStatusCode();
        if (($resCode > 300) || ($resCode < 200)) {
            return false;
        }

        $responseData = json_decode($response->getBody());

        if (!$responseData || (!isset($responseData['id']))) {
            return false;
        }

        return $responseData['id'];

    }


}