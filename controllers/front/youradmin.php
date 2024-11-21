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

class Sd_YourioYouradminModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if (ConfigurationHelper::getYourApiKey() && ConfigurationHelper::getYourApiKey() != '') {
            $yourApi = new YourAPI();
            $htmlData = $yourApi->getIndexPage();
            $htmlData = YourAPI::assignSubPath($htmlData);
            echo $htmlData;
        } else {
            echo 'API Error';
        }
        exit;
    }
}
