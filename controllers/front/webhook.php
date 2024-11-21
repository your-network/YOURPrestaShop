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
 * @author YOURAI
 * @copyright 2020-2024 YOURAI
 * @license LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Sd_YourioWebhookModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        header('Content-Type: application/json');

        // Check the API key in the request
        if (!$this->isValidApiKey()) {
            $this->handleError('Unauthorized access. Invalid API key.', 403);

            return;
        }

        $this->processWebhook();

        $response = [
            'status' => 'success',
            'message' => 'Snippet Code have been updated.',
        ];

        $this->sendResponse($response, 200);
    }

    /**
     * @return void
     */
    public function processWebhook()
    {
        $languages = Language::getLanguages(true);
        $yourApi = new YourAPI();

        foreach ($languages as $lang) {
            $locale = $lang['iso_code'];
            $embedded_snippet_code = $yourApi->getEmbedSnippetCode(ConfigurationHelper::getYourApiKey(), $locale);
            $jsFilePath = _PS_MODULE_DIR_ . 'sd_yourio/views/js/snippet_' . $locale . '.js';

            if (!$this->isJson($embedded_snippet_code['response'])) {
                file_put_contents($jsFilePath, $embedded_snippet_code['response']);
            }
        }
    }

    protected function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Handle errors during webhook processing.
     *
     * @param string $message error message to display
     * @param int $statusCode HTTP status code to return
     */
    protected function handleError($message, $statusCode = 500)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        $this->sendResponse($response, $statusCode);
    }

    /**
     * Send JSON response with a specific HTTP status code.
     *
     * @param array $response the response data to send
     * @param int $statusCode HTTP status code
     */
    protected function sendResponse($response, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($response);
        exit;
    }

    /**
     * Check if the provided API key in the request is valid.
     *
     * @return bool
     */
    private function isValidApiKey()
    {
        $providedApiKey = Tools::getValue('api-key');

        return $providedApiKey === ConfigurationHelper::getWebServiceKey();
    }
}
