<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    YOURAI
 * @copyright 2020-2024 YOURAI
 * @license LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class YourAPI
{
    /**
     * Host and versions
     */
    public const BASE_URL = 'https://quickly-smart-macaque.ngrok-free.app';
    public const YOUR_API_VERSION = 'v1';
    /**
     * Request options
     */
    public const HTTP_POST = 'POST';
    public const HTTP_PATCH = 'PATCH';
    public const HTTP_GET = 'GET';
    public const APPLICATION_JSON_HEADER_VALUE = 'application/json';
    public const TEXT_PLAIN_VALUE = 'text/plain';
    public const CONTENT_TYPE_HEADER = 'Content-Type';
    public const ACCEPT_KEY_HEADER = 'accept';
    public const AUTHORIZATION_KEY_HEADER = 'Authorization';
    public const CONTENT_LENGTH_HEADER = 'Content-Length';
    public const REGISTRATION_API_KEY = 'afc29d1a-8f4b-4e99-a8b9-3e23f7f7602d';
    public const ERROR_MALFORMED_RESPONSE_BODY = 'Response from API could not be decoded from JSON, check response body';
    
    /**
     *  Build headers for the all API calls
     *
     * @return array
     */
    public function getHeaders($body = null, $authorization = null, $accept = null)
    {
        $apiKey = 'Basic ' . ($authorization ?? self::REGISTRATION_API_KEY);

        $headers = [
            self::ACCEPT_KEY_HEADER . ': ' . ($accept ?? self::TEXT_PLAIN_VALUE),
            self::CONTENT_TYPE_HEADER . ': ' . self::APPLICATION_JSON_HEADER_VALUE,
            self::AUTHORIZATION_KEY_HEADER . ': ' . $apiKey,
        ];

        return $headers;
    }

    /**
     * @param $path
     * @param $method
     * @param $body
     *
     * @return array|string|null
     */
    public function request($path, $method, $body = null, $authorization = null, $accept = null, $isJson = true, $isHtml = false, $isValidate = false)
    {
        $curl = curl_init();

        $options = [
            CURLOPT_URL => self::BASE_URL . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->getHeaders($body, $authorization, $accept),
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if ($method === 'POST' || $method === 'PATCH') {
            $options[CURLOPT_POSTFIELDS] = json_encode($body);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $isHtml ? $response : ['response' => $response, 'status_code' => $statusCode];
    }

    /**
     * Get base options array for curl request.
     *
     * @return array
     */
    #[ReturnTypeWillChange]
    protected function getDefaultCurlOptions($method = self::HTTP_POST)
    {
        return [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
        ];
    }

    /**
     * @param $activity
     *
     * @return array|string|void|null
     */
    public function getIndexPage()
    {
        return $this->request('/prestashop', self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/html', false, true);
    }

    /**
     * @param $activity
     *
     * @return array|string|void|null
     */
    public function getShopActivityTopics($activity = null)
    {
        $path = '/Prestashop/Activity/Shop/Topic';
        $path .= $activity ? '?topic=' . $activity : '';

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $topic
     * @param $step
     * @param $completed
     *
     * @return array|string|null
     */
    public function postShopActivityTopics($topic, $step, $completed)
    {
        $body = [
            'topic' => $topic,
            'step' => $step,
            'completed' => $completed,
        ];

        return $this->request('/Prestashop/Activity/Shop/Topic', self::HTTP_POST, $body, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @return array|string|null
     */
    public function getSubscriptionModels()
    {
        return $this->request('/Prestashop/Subscription/Models', self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @return array|string|null
     */
    public function getSubscription()
    {
        return $this->request('/Prestashop/Subscription', self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $start
     * @param $end
     *
     * @return array|false|string|null
     */
    public function getShopStatsDashboard($start, $end)
    {
        if (!$start && !$end) {
            return json_encode(['status' => true, 'message' => 'start and end data are missing']);
        }
        $path = '/Prestashop/Stats/Dashboard?start=' . $start . '&end=' . $end . '&lang=' . ConfigurationHelper::getLanguageCode();

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @return array|string|null
     */
    public function postCatalogDownload($downloadType = 'DemoCatalog')
    {
        $path = '/Prestashop/Catalog/Download';
        $body = [
            'downloadType' => $downloadType,
        ];

        return $this->request($path, self::HTTP_POST, $body, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    public function postShopBillingDetails($payloadData)
    {
        $path = '/Prestashop/Shop/Billing/Details';

        return $this->request($path, self::HTTP_POST, $payloadData, ConfigurationHelper::getYourApiKey(), 'text/plain', true, false, true);
    }

    public function patchShopStyling($payloadData)
    {
        $path = '/Prestashop/Styling';

        return $this->request($path, self::HTTP_PATCH, $payloadData, ConfigurationHelper::getYourApiKey(), 'text/plain', true, false, true);
    }

    public function getShopBillingDetails()
    {
        $path = '/Prestashop/Shop/Billing/Details';

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @return array|string|null
     */
    public function getShopStyling()
    {
        $path = '/Prestashop/Styling';

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $payloadData
     *
     * @return array|string|null
     */
    public function postStripeSetupIntent($payloadData)
    {
        $path = '/Prestashop/Payment/Stripe/SetupIntent';

        return $this->request($path, self::HTTP_POST, $payloadData, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $params
     *
     * @return array|string|null
     */
    public function getStripeSetupIntent($params)
    {
        $path = '/Prestashop/Payment/Stripe/SetupIntent?successUrl=' . urlencode($params['successUrl']) . '&cancelUrl=' . urlencode($params['cancelUrl']);

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $payloadData
     *
     * @return array|string|null
     */
    public function postSubscription($payloadData)
    {
        $path = '/Prestashop/Subscription';

        return $this->request($path, self::HTTP_POST, $payloadData, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $payloadData
     *
     * @return array|string|null
     */
    public function postSubscriptionDowngrade($payloadData)
    {
        $path = '/Prestashop/Subscription/Downgrade';

        return $this->request($path, self::HTTP_POST, $payloadData, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $subscriptionModelId
     *
     * @return array|string|null
     */
    public function getSubscriptionCostPrediction($subscriptionModelId)
    {
        $path = '/Prestashop/Subscription/CostPrediction?subscriptionModelId=' . $subscriptionModelId;

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @return array|string|null
     */
    public function getCatalogDownload()
    {
        $path = '/Prestashop/Catalog/Download';

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $start
     * @param $end
     * @param $interval
     *
     * @return array|false|string|null
     */
    public function getShopStatsGraph($start, $end, $interval = 'D')
    {
        if (!$start && !$end) {
            return json_encode(['status' => true, 'message' => 'start and end data are missing']);
        }
        $path = '/Prestashop/Stats/Requests/Graph?start=' . $start . '&end=' . $end . '&lang=' . ConfigurationHelper::getLanguageCode() . '&interval=' . $interval;

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $start
     * @param $end
     *
     * @return array|false|string|null
     */
    public function getShopStatsProducts($start, $end)
    {
        if (!$start && !$end) {
            return json_encode(['status' => true, 'message' => 'start and end data are missing']);
        }
        $path = '/Prestashop/Stats/Requests/Products?start=' . $start . '&end=' . $end . '&lang=' . ConfigurationHelper::getLanguageCode();

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @param $answeredByUserOnly
     *
     * @return array|string|null
     */
    public function getShopContentBlocks($answeredByUserOnly = 'false')
    {
        $path = '/Prestashop/ContentBlocks?answeredByUserOnly=' . $answeredByUserOnly;

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain');
    }

    /**
     * @return array|string|null
     */
    public function postShopRegister()
    {
        $body = [
            'prestaShopId' => ConfigurationHelper::getWebServiceKey(),
            'prestaShopApiKey' => ConfigurationHelper::getWebServiceKey(),
            'prestashopApiUrl' => ConfigurationHelper::getWebserviceUrl(),
            'embedWebhookUrl' => ConfigurationHelper::getStyleWebhookUrl(),
            'organization' => [
                'name' => ConfigurationHelper::getShopName(),
                'address' => ConfigurationHelper::getShopAddress(),
                'city' => ConfigurationHelper::getShopCity(),
                'zipCode' => ConfigurationHelper::getShopZipCode(),
                'country' => ConfigurationHelper::getShopCountry(),
                'phoneNumber' => ConfigurationHelper::getShopPhoneNumber(),
                'currencyCode' => ConfigurationHelper::getCurrencyCode(),
                'contentLanguage' => ConfigurationHelper::getLanguageCode(),
                'website' => ConfigurationHelper::getShopUrl(),
                'notifications' => ['email' => true],
            ],
            'user' => [
                'personalName' => ConfigurationHelper::getShopName(),
                'website' => ConfigurationHelper::getShopUrl(),
                'email' => ConfigurationHelper::getShopEmail(),
                'phoneNumber' => ConfigurationHelper::getShopPhoneNumber(),
            ],
        ];

        return $this->request('/Prestashop/Shop/Register', self::HTTP_POST, $body);
    }

    /**
     * @param $apiKey
     *
     * @return array|string|null
     */
    public function getEmbedSnippetCode($apiKey, $locale = 'en', $i = 0)
    {
        return $this->request('/Prestashop/Embed/Snippet/' . $locale . '.js?i=' . $i, self::HTTP_GET, null, $apiKey, 'text/javascript', false);
    }

    /**
     * @param $type
     * @param $matchId
     * @param $lang
     *
     * @return array|string|null
     */
    public function getProductContentBlocks($type, $matchId, $lang = 'en')
    {
        if (!$type && !$matchId) {
            return ['success' => false, 'error' => 'Invalid Request'];
        }
        $path = '/Prestashop/Product/' . $type . '?prestaShopId=' . ConfigurationHelper::getWebServiceKey() . '&matchId=' . $matchId . '&lang=' . $lang;

        return $this->request($path, self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain', false, false, true);
    }

    /**
     * @return array|string|null
     */
    public function getCatalogProductPreview()
    {
        return $this->request('/Prestashop/Catalog/Product/Preview', self::HTTP_GET, null, ConfigurationHelper::getYourApiKey(), 'text/plain', false);
    }

    /**
     * Handle the API response and return the parsed data.
     *
     * @param string $response the raw API response
     * @param int $statusCode the HTTP status code of the response
     *
     * @return array|string|null An array containing the parsed data or null on error
     */
    protected function handleAPIResponse($response, $statusCode)
    {
        $decoded_response = $this->decodeJsonResponse($response);

        if ($statusCode == 500 || $statusCode == 400) {
            $message = $statusCode;
            if (isset($decoded_response['errors']) && $decoded_response['errors']) {
                foreach ($decoded_response['errors'] as $key => $error) {
                    $message .= ' | ' . $key . ' - ' . $error;
                }
            }

            PrestaShopLogger::addLog('YourAPI - ' . $message, 3, null, null, null, true);
        } elseif ($statusCode < 200 || $statusCode >= 300) {
            $message = $statusCode;
            if (isset($decoded_response['errors']) && $decoded_response['errors']) {
                foreach ($decoded_response['errors'] as $key => $error) {
                    $message .= ' | ' . $key . ' - ' . $error;
                }
            }

            PrestaShopLogger::addLog('YourAPI - ' . $message, 2, null, null, null, true);
        }

        return $decoded_response;
    }

    /**
     * @param $response
     *
     * @return mixed
     */
    private function decodeJsonResponse($response)
    {
        if (!empty($response)) {
            try {
                return json_decode($response, true);
            } catch (Exception $e) {
                PrestaShopLogger::addLog('YourAPI: ' . self::ERROR_MALFORMED_RESPONSE_BODY . ' - status: ' . $statusCode, 2, null, null, null, true);
            }
        }

        return json_decode('{}', true);
    }

    /**
     * @param $response
     *
     * @return array|string|string[]
     */
    public static function assignSubPath($response)
    {
        return str_replace('__YOUR_INTEGRATION_SUBPATH__', 'your-app/', $response);
    }
}
