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
class YourOrganizations extends ObjectModel
{
    public static $definition = [
        'table' => 'your_organization',
        'primary' => 'id_your_organization',
        'fields' => [
            'email_address' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255],
            'phone_number' => ['type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32],
            'personal_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'address_1' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'address_2' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'city' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'country' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'currency_code' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'language_code' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'zip_code' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
            'website' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255],
        ],
    ];

    /** @var array Web service parameters */
    protected $webserviceParameters = [
        'objectNodeName' => 'your_organizations',
        'fields' => [
            'email_address' => [
                'getter' => 'getWsEmailAddress',
            ],
            'phone_number' => [
                'getter' => 'getWsPhoneNumber',
            ],
            'personal_name' => [
                'getter' => 'getWsPersonalName',
            ],
            'name' => [
                'getter' => 'getWsName',
            ],
            'address_1' => [
                'getter' => 'getWsAddress1',
            ],
            'address_2' => [
                'getter' => 'getWsAddress2',
            ],
            'city' => [
                'getter' => 'getWsCity',
            ],
            'country' => [
                'getter' => 'getWsCountry',
            ],
            'currency_code' => [
                'getter' => 'getWsCurrencyCode',
            ],
            'language_code' => [
                'getter' => 'getWsLanguageCode',
            ],
            'zip_code' => [
                'getter' => 'getWsZipCode',
            ],
            'website' => [
                'getter' => 'getWsWebsite',
            ],
        ],
    ];

    public function getWsEmailAddress()
    {
        return Configuration::get('PS_SHOP_EMAIL');
    }

    public function getWsPhoneNumber()
    {
        return Configuration::get('PS_SHOP_PHONE');
    }

    public function getWsPersonalName()
    {
        return str_replace(' ', '_', strtolower(Configuration::get('PS_SHOP_NAME')));
    }

    public function getWsName()
    {
        return Configuration::get('PS_SHOP_NAME');
    }

    public function getWsAddress1()
    {
        return Configuration::get('PS_SHOP_ADDR1');
    }

    public function getWsAddress2()
    {
        return Configuration::get('PS_SHOP_ADDR2');
    }

    public function getWsCity()
    {
        return Configuration::get('PS_SHOP_CITY');
    }

    public function getWsCountry()
    {
        return Country::getNameById(Context::getContext()->language->id, Configuration::get('PS_SHOP_COUNTRY_ID'));
    }

    public function getWsCurrencyCode()
    {
        return Context::getContext()->currency->iso_code;
    }

    public function getWsLanguageCode()
    {
        return Context::getContext()->language->language_code;
    }

    public function getWsZipCode()
    {
        return Configuration::get('PS_SHOP_CODE');
    }

    public function getWsWebsite()
    {
        return Context::getContext()->shop->getBaseURL();
    }
}
