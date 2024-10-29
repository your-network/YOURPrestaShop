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

class ConfigurationHelper
{
    public const _BEARER_TOKEN_ = 'SD_YOURIO_BEARER_TOKEN';
    public const _PS_WEBSERVICE_ = 'PS_WEBSERVICE';
    public const _YOUR_WEBSERVICE_KEY = 'SD_YOURIO_WEBSERVICE_KEY';
    public const SHOP_NAME = 'PS_SHOP_NAME';
    public const SHOP_ADDRESS_1 = 'PS_SHOP_ADDR1';
    public const SHOP_ADDRESS_2 = 'PS_SHOP_ADDR2';
    public const SHOP_PHONE = 'PS_SHOP_PHONE';
    public const SHOP_EMAIL = 'PS_SHOP_EMAIL';
    public const SHOP_CITY = 'PS_SHOP_CITY';
    public const SHOP_COUNTRY_ID = 'PS_SHOP_COUNTRY_ID';
    public const SHOP_ZIP_CODE = 'PS_SHOP_CODE';
    public const YOUR_API_KEY = 'SD_YOUR_API_KEY';
    public const PRODUCT_DESC = 'SD_YOUR_PRODUCT_DESCRIPTION';
    public const PRODUCT_IMAGES = 'SD_YOUR_PRODUCT_IMAGES';
    public const PRODUCT_BULLETS = 'SD_YOUR_PRODUCT_BULLETS';
    public const PRODUCT_PROS_CONS = 'SD_YOUR_PRODUCT_PROS_CONS';
    public const PRODUCT_QA = 'SD_YOUR_PRODUCT_QA';
    public const PRODUCT_REASON_TO_BUY = 'SD_YOUR_PRODUCT_REASONTOBUY';
    public const PRODUCT_REVIEWS = 'SD_YOUR_PRODUCT_REVIEWS';
    public const PRODUCT_SPECIFICATIONS = 'SD_YOUR_PRODUCT_SPECIFICATIONS';
    public const PRODUCT_VIDEO = 'SD_YOUR_PRODUCT_VIDEO';
    public const PRODUCT_PDF = 'SD_YOUR_PRODUCT_PDF';
    public const MODULE_NAME = 'sd_yourio';
    public const WEBHOOK_CONTROLLER = 'webhook';

    /**
     * @return void
     */
    public static function installConfiguration()
    {
        Configuration::updateValue(self::_PS_WEBSERVICE_, 1);
    }

    /**
     * @return void
     */
    public static function uninstallConfiguration()
    {
        Configuration::deleteByName(self::YOUR_API_KEY);
    }

    /**
     * @return string
     */
    public static function getWebServiceKey()
    {
        return Configuration::get(self::_YOUR_WEBSERVICE_KEY);
    }

    /**
     * @param $webServiceKey
     *
     * @return void
     */
    public static function setWebServiceKey($webServiceKey)
    {
        Configuration::updateValue(self::_YOUR_WEBSERVICE_KEY, $webServiceKey);
    }

    /**
     * @return false|string
     */
    public static function getShopPhoneNumber()
    {
        return Configuration::get(self::SHOP_PHONE);
    }

    /**
     * @return string
     */
    public static function getShopAddress()
    {
        return Configuration::get(self::SHOP_ADDRESS_1) . ' ' . Configuration::get(self::SHOP_ADDRESS_2);
    }

    /**
     * @return false|string
     */
    public static function getShopEmail()
    {
        return Configuration::get(self::SHOP_EMAIL);
    }

    /**
     * @return false|string
     */
    public static function getShopCity()
    {
        return Configuration::get(self::SHOP_CITY);
    }

    /**
     * @return string
     */
    public static function getShopCountry()
    {
        return Country::getNameById(Context::getContext()->language->id, Configuration::get(self::SHOP_COUNTRY_ID));
    }

    /**
     * @return string
     */
    public static function getCurrencyCode()
    {
        return Context::getContext()->currency->iso_code;
    }

    /**
     * @return string
     */
    public static function getLanguageCode()
    {
        return Context::getContext()->language->iso_code;
    }

    /**
     * @return bool|string
     */
    public static function getShopUrl()
    {
        return Context::getContext()->shop->getBaseURL();
    }

    /**
     * @return false|string
     */
    public static function getShopZipCode()
    {
        return Configuration::get(self::SHOP_ZIP_CODE);
    }

    /**
     * @return false|string
     */
    public static function getShopName()
    {
        return Configuration::get(self::SHOP_NAME);
    }

    /**
     * @param $yourApiKey
     *
     * @return void
     */
    public static function setYourApiKey($yourApiKey)
    {
        Configuration::updateValue(self::YOUR_API_KEY, $yourApiKey);
    }

    /**
     * @return false|string
     */
    public static function getYourApiKey()
    {
        return Configuration::get(self::YOUR_API_KEY);
    }

    /**
     * @return string
     */
    public static function getWebserviceUrl()
    {
        return Context::getContext()->shop->getBaseURL() . 'api';
    }

    /**
     * @return string
     */
    public static function getStyleWebhookUrl()
    {
        return Context::getContext()->link->getModuleLink(self::MODULE_NAME, self::WEBHOOK_CONTROLLER);
    }

    /**
     * @return string[]
     */
    public static function getContentBlocks()
    {
        return [
            self::PRODUCT_DESC => '<div data-your="product-description"></div>',
            self::PRODUCT_BULLETS => '<div data-your="product-bullets"></div>',
            self::PRODUCT_IMAGES => '<div data-your="product-images"></div>',
            self::PRODUCT_PDF => '<div data-your="product-pdf"></div>',
            self::PRODUCT_PROS_CONS => '<div data-your="product-pros-cons"></div>',
            self::PRODUCT_QA => '<div data-your="product-qa"></div>',
            self::PRODUCT_REASON_TO_BUY => '<div data-your="product-reasons-to-buy"></div>',
            self::PRODUCT_REVIEWS => '<div data-your="product-reviews"></div>',
            self::PRODUCT_SPECIFICATIONS => '<div data-your="product-specifications"></div>',
            self::PRODUCT_VIDEO => '<div data-your="product-videos"></div>',
        ];
    }
}
