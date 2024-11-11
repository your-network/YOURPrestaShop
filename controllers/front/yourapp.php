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
 * @author    Simul Digital
 * @copyright 2020-2024 Simul Digital
 * @license LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Sd_YourioYourappModuleFrontController extends ModuleFrontController
{
    /**
     * @return void
     */
    public function postProcess()
    {
        $endpointParam = Tools::getValue('endpoint_param');

        if ($endpointParam) {
            if ($endpointParam == 'Shop/Register') {
                $this->handleShopRegisterBlocks();
            } else {
                $endpointParam = '/Prestashop/' . $endpointParam;
                $body = (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PATCH'])) ? json_decode(Tools::file_get_contents('php://input'), true) : null;

                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $params = Tools::getAllValues();

                    foreach (['endpoint_param', 'fc', 'module', 'controller'] as $excludedParam) {
                        unset($params[$excludedParam]);
                    }

                    $queryString = http_build_query($params);
                    if ($queryString && $queryString != '') {
                        $endpointParam .= $queryString ? '?' . $queryString : '';
                    }
                }

                $yourApi = new YourAPI();
                $response = $yourApi->request(
                    $endpointParam,
                    $_SERVER['REQUEST_METHOD'],
                    $body,
                    ConfigurationHelper::getYourApiKey()
                );

                $this->sendResponse($response['response'], $response['status_code']);
            }
        } else {
            $this->handleError('Endpoint parameter missing', 400);
        }

        // $handler = $this->getHandlerForEndpoint($endpointParam);

        /*if ($handler && method_exists($this, $handler)) {
            $this->$handler($endpointParam);
        } else {
            $this->handleError('Endpoint not found: ' . htmlspecialchars($endpointParam), 404);
        }*/
    }

    /**
     * @param $endpointParam
     *
     * @return string|null
     */
    private function getHandlerForEndpoint($endpointParam)
    {
        foreach ($this->handlers as $key => $handler) {
            if (strpos($endpointParam, $key) === 0) {
                return $handler;
            }
        }

        return null;
    }

    private $handlers = [
        'prestashop' => 'handleAdminDashboard',
        'Stats/Dashboard' => 'handleStatsDashboard',
        'Stats/Requests/Graph' => 'handleStatsGraph',
        'Activity/Shop/Topic' => 'handleShopActivity',
        'Stats/Requests/Products' => 'handleStatsProducts',
        'ContentBlocks' => 'handleContentBlocks',
        'Shop/Register' => 'handleShopRegisterBlocks',
        'Subscription/Models' => 'handleSubscriptionModels',
        'ShopifyCatalog/Download' => 'handleCatalogDownload',
        'Catalog/Download' => 'handleCatalogDownload',
        'Product/' => 'handleProductRequest',
        'Payment/Stripe/SetupIntent' => 'handleStripeSetupIntent',
        'Shop/Billing/Details' => 'handleShopBillingDetails',
        'Subscription/CostPrediction' => 'handleSubscriptionCostPrediction',
        'Subscription/Downgrade' => 'handleSubscriptionDowngrade',
        'Styling' => 'handleStyling',
        'Subscription' => 'handleSubscription',
        'Catalog/Product/Preview' => 'handleCatalogProductPreview',
        'embed/snippet' => 'handleEmbedSnippetCode',
    ];

    /**
     * @param $endpointParam
     *
     * @return void
     */
    private function handleProductRequest($endpointParam)
    {
        $type = str_replace('Product/', '', $endpointParam);
        $yourApi = new YourAPI();
        $matchId = Tools::getValue('matchId');
        $lang = Tools::getValue('lang');
        $response = $yourApi->getProductContentBlocks($type, $matchId, $lang);
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleSubscriptionModels()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getSubscriptionModels();
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleCatalogDownload()
    {
        $yourApi = new YourAPI();
        if ($_SERVER['REQUEST_METHOD'] == YourAPI::HTTP_POST) {
            $payloadData = json_decode(Tools::file_get_contents('php://input'), true);
            $response = $yourApi->postCatalogDownload($payloadData['downloadType']);
        } else {
            $response = $yourApi->getCatalogDownload();
        }
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleSubscription()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getSubscription();
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleShopRegisterBlocks()
    {
        $response = $this->shopRegister();
        echo json_encode($response);
        exit;
    }

    private function handleEmbedSnippetCode()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getEmbedSnippetCode(ConfigurationHelper::getYourApiKey(), Tools::getValue('locale', 'en'), Tools::getValue('i', 0));
        $response['response'] = YourAPI::assignSubPath($response['response']);
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleContentBlocks()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getShopContentBlocks(Tools::getValue('answeredByUserOnly') ? Tools::getValue('answeredByUserOnly') : 'false');
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleStatsGraph()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getShopStatsGraph(Tools::getValue('start'), Tools::getValue('end'), Tools::getValue('interval'));
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleStatsProducts()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getShopStatsProducts(Tools::getValue('start'), Tools::getValue('end'));
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleStatsDashboard()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getShopStatsDashboard(Tools::getValue('start'), Tools::getValue('end'));
        $this->sendResponse($response['response'], $response['status_code']);
    }

    private function handleSubscriptionCostPrediction()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getSubscriptionCostPrediction(Tools::getValue('subscriptionModelId'));
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleShopActivity()
    {
        $yourApi = new YourAPI();
        if ($_SERVER['REQUEST_METHOD'] == YourAPI::HTTP_POST) {
            $payloadData = json_decode(Tools::file_get_contents('php://input'), true);
            $response = $yourApi->postShopActivityTopics($payloadData['topic'], $payloadData['step'], $payloadData['completed']);
            header('Content-Type: application/json');
            echo json_encode(json_decode($response, true) + ['is_post_activity' => 'true']);
            exit;
        } else {
            $response = $yourApi->getShopActivityTopics(Tools::getValue('topic'));
            $this->sendResponse($response['response'], $response['status_code']);
        }
    }

    /**
     * @return array|string|string[]|null
     */
    private function handleAdminDashboard()
    {
        if (ConfigurationHelper::getYourApiKey() && ConfigurationHelper::getYourApiKey() != '') {
            $yourApi = new YourAPI();

            return $yourApi->getIndexPage();
        } else {
            return [
                'status' => 'Error',
                'message' => 'issue with API Key.',
            ];
        }
    }

    /**
     * @param $message
     * @param $statusCode
     *
     * @return void
     */
    private function handleError($message, $statusCode = 404)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];
        $this->sendResponse($response, $statusCode);
    }

    /**
     * @param $response
     * @param $statusCode
     *
     * @return void
     */
    private function sendResponse($response, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo $response;
        exit;
    }

    /**
     * @return mixed
     */
    public function shopRegister()
    {
        $permissions = [
            'products' => ['GET' => 1, 'POST' => 1, 'PUT' => 1, 'PATCH' => 1, 'DELETE' => 1, 'HEAD' => 1],
            'categories' => ['GET' => 1, 'POST' => 1, 'PUT' => 1, 'PATCH' => 1, 'DELETE' => 1, 'HEAD' => 1],
            'combinations' => ['GET' => 1, 'POST' => 1, 'PUT' => 1, 'PATCH' => 1, 'DELETE' => 1, 'HEAD' => 1],
            'languages' => ['GET' => 1, 'POST' => 1, 'PUT' => 1, 'PATCH' => 1, 'DELETE' => 1, 'HEAD' => 1],
            'your_organization' => ['GET' => 1, 'POST' => 1, 'PUT' => 1, 'PATCH' => 1, 'DELETE' => 1, 'HEAD' => 1],
        ];

        $webservice_id = WebserviceKey::getIdFromKey(ConfigurationHelper::getWebServiceKey());
        WebserviceKey::setPermissionForAccount($webservice_id, $permissions);

        $shop_register = new YourAPI();
        $request_result = $shop_register->postShopRegister();
        $request_result = json_decode($request_result['response'], true);

        if (isset($request_result['success']) && $request_result['success']) {
            if (isset($request_result['apiKey']) && $request_result['apiKey']) {
                ConfigurationHelper::setYourApiKey($request_result['apiKey']);
            }
        }

        return $request_result;
    }

    /**
     * @return void
     */
    private function handleStripeSetupIntent()
    {
        $yourApi = new YourAPI();
        if ($_SERVER['REQUEST_METHOD'] == YourAPI::HTTP_POST) {
            $payloadData = json_decode(Tools::file_get_contents('php://input'), true);
            $response = $yourApi->postStripeSetupIntent($payloadData);
        } else {
            $response = $yourApi->getStripeSetupIntent(Tools::getAllValues());
        }
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleSubscriptionDowngrade()
    {
        $yourApi = new YourAPI();
        if ($_SERVER['REQUEST_METHOD'] == YourAPI::HTTP_POST) {
            $payloadData = json_decode(Tools::file_get_contents('php://input'), true);
            $response = $yourApi->postSubscriptionDowngrade($payloadData);
        }
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleShopBillingDetails()
    {
        $yourApi = new YourAPI();
        if ($_SERVER['REQUEST_METHOD'] == YourAPI::HTTP_POST) {
            $payloadData = json_decode(Tools::file_get_contents('php://input'), true);
            $response = $yourApi->postShopBillingDetails($payloadData);
            $this->sendResponse($response['response'], $response['status_code']);
        } else {
            $response = $yourApi->getShopBillingDetails();
        }
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleStyling()
    {
        $yourApi = new YourAPI();
        if ($_SERVER['REQUEST_METHOD'] == YourAPI::HTTP_PATCH) {
            $payloadData = json_decode(Tools::file_get_contents('php://input'), true);
            $response = $yourApi->patchShopStyling($payloadData);
            $this->sendResponse($response['response'], $response['status_code']);
        } else {
            $response = $yourApi->getShopStyling();
        }
        $this->sendResponse($response['response'], $response['status_code']);
    }

    /**
     * @return void
     */
    private function handleCatalogProductPreview()
    {
        $yourApi = new YourAPI();
        $response = $yourApi->getCatalogProductPreview();
        $this->sendResponse($response['response'], $response['status_code']);
    }
}
