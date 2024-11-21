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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

require_once dirname(__FILE__) . '/autoload.php';

class Sd_yourio extends Module implements WidgetInterface
{
    /**
     * @var string
     */
    private $output;
    public const YOURONBOARDING_ADMIN_CONTROLLER = 'AdminYourOnBoarding';
    protected $hooks_list = [
        '0' => 'none',
        'displayProductPriceBlock|after_price' => 'displayProductPriceBlock -> after_price',
        'displayProductPriceBlock|weight' => 'displayProductPriceBlock -> weight',
        'displayProductPriceBlock|price' => 'displayProductPriceBlock -> price',
        'displayProductListReviews' => 'displayProductListReviews',
        'displayProductAdditionalInfo' => 'displayProductAdditionalInfo',
        'displayFooterProduct' => 'displayFooterProduct',
        'displayAfterProductThumbs' => 'displayAfterProductThumbs',
        'displayYourBlock' => 'displayYourBlock - Custom Hook',
    ];

    protected $register_hook_list = [
        'displayHeader',
        'displayProductPriceBlock',
        'displayProductListReviews',
        'displayProductAdditionalInfo',
        'displayAfterProductThumbs',
        'displayFooterProduct',
        'displayYourBlock',
        'addWebserviceResources',
        'moduleRoutes',
        'displayBackOfficeHeader',
    ];

    public function __construct()
    {
        $this->name = 'sd_yourio';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'YOURAI';
        $this->need_instance = 0;
        parent::__construct();
        $this->output = '';
        $this->displayName = $this->l('YOUR Product Content');
        $this->description = $this->l('Easy integration of product descriptions, images, videos, reviews and question &amp; answers with YOUR');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        ConfigurationHelper::installConfiguration();
        $this->installDbContent();
        if (!ConfigurationHelper::getWebServiceKey()) {
            $this->createYourWebservice();
        }

        return parent::install() && $this->registerHook($this->register_hook_list);
    }

    public function uninstall()
    {
        $sql = 'DROP TABLE IF EXISTS  `' . _DB_PREFIX_ . 'your_organization`;';
        Db::getInstance()->execute($sql);
        ConfigurationHelper::uninstallConfiguration();

        return parent::uninstall();
    }

    public function getContent()
    {
        return $this->_getSettingsContent();
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            Configuration::updateValue('SD_YOURIO_CONF_URL', $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name);
            $this->context->controller->addJS($this->_path . 'views/js/vue.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addJS($this->_path . 'views/js/menu.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');

            $moduleAdminLink = Context::getContext()->link->getAdminLink(
                'AdminModules',
                true,
                false,
                ['configure' => $this->name]
            );
            $currentPage = Tools::getValue('currentPage') ? Tools::getValue('currentPage') : 'yourDashboardConfiguration';
            // Assigning JS variables
            Media::addJsDef(
                [
                    'currentPage' => $currentPage,
                    'moduleAdminLink' => $moduleAdminLink,
                    'dashboard' => Context::getContext()->link->getModuleLink('sd_yourio', 'youradmin'),
                    'admin_ajax_controller' => $this->context->link->getAdminLink('AdminYourOnBoarding'),
                    'shop_register_proxy_url' => $this->context->link->getModuleLink($this->name, 'yourapp', ['endpoint_param' => 'Shop/Register']),
                    'index_dashboard_url' => $this->context->link->getModuleLink($this->name, 'yourapp', ['endpoint_param' => 'prestashop']),
                    'index_base' => $this->context->link->getModuleLink($this->name, 'yourapp', ['endpoint_param' => '']),
                ]
            );
        }
    }

    /**
     * @return string|void
     */
    public function hookDisplayHeader()
    {
        if ($this->context->controller->php_self == 'product') {
            $locale = ConfigurationHelper::getLanguageCode();
            $jsFilePath = _PS_MODULE_DIR_ . 'sd_yourio/views/js/snippet_' . $locale . '.js';
            if (!file_exists($jsFilePath) || filesize($jsFilePath) == 0) {
                $yourApi = new YourAPI();
                $embedded_snippet_code = $yourApi->getEmbedSnippetCode(ConfigurationHelper::getYourApiKey(), $locale);
                file_put_contents($jsFilePath, $embedded_snippet_code);
            }

            $product = new Product((int) Tools::getValue('id_product'));

            $this->context->smarty->assign(['current_product' => $product, 'locale' => $locale]);
            $this->context->controller->addJS($jsFilePath);

            return $this->fetch('module:' . $this->name . '/views/templates/hook/snippet_js_code.tpl');
        }
    }

    /**
     * @param $params
     *
     * @return array[]
     */
    public function hookAddWebserviceResources($params)
    {
        return [
            'your_organizations' => [
                'description' => $this->trans('Your Organization', [], 'Admin.Modules.YourIO'),
                'class' => $this->trans('YourOrganizations', [], 'Admin.Modules.YourIO'),
            ],
        ];
    }

    /**
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function createYourWebservice()
    {
        $webservice_key = strtoupper($this->generateRandomKey());
        $webservice = new WebserviceKey();
        $webservice->key = $webservice_key;
        $webservice->description = $this->trans('Your.IO API', [], 'Admin.Modules.YourIO');
        $webservice->active = true;
        $webservice->save();

        ConfigurationHelper::setWebServiceKey($webservice_key);
    }

    /**
     * @param $length
     *
     * @return string
     *
     * @throws Random\RandomException
     */
    public function generateRandomKey($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * @return void
     */
    public function installDbContent()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'your_organization` (`id_your_organization` INT NOT NULL ) ENGINE = InnoDB;';
        Db::getInstance()->execute($sql);
        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'your_organization` (`id_your_organization`) VALUES (1)');
    }

    /**
     * @return array[]
     */
    public function hookModuleRoutes()
    {
        $prefix = 'your-app';

        return [
            'module-sd_yourio-yourapp' => [
                'controller' => 'yourapp',
                'rule' => "$prefix/{endpoint_param}",
                'keywords' => [
                    'endpoint_param' => [
                        'regexp' => '.*',
                        'param' => 'endpoint_param',
                    ],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                    'controller' => 'yourapp',
                ],
            ],
            'module-sd_yourio-webhook' => [
                'controller' => 'webhook',
                'rule' => 'your-app-style',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
            'module-sd_yourio-youradmin' => [
                'controller' => 'youradmin',
                'rule' => 'your-prestashop',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
        ];
    }

    /**
     * @return false|string|void
     *
     * @throws SmartyException
     */
    private function _getSettingsContent()
    {
        $tpl = Context::getContext()->smarty->createTemplate(_PS_MODULE_DIR_ . 'sd_yourio/views/templates/admin/index.tpl');
        $this->postProcess(); // execute submit form

        $dashboardHTML = '';
        if (ConfigurationHelper::getYourApiKey()) {
            $yourApi = new YourAPI();
            $dashboardHTML = $yourApi->getIndexPage();
            $dashboardHTML = YourApi::assignSubPath($dashboardHTML);
        }
        $tpl->assign([
            'img_dir' => _MODULE_DIR_ . 'sd_yourio/views/img',
            'dashboardHTML' => $dashboardHTML,
            'api_key' => ConfigurationHelper::getYourApiKey(),
            'id_shop' => Context::getContext()->shop->id,
            'id_lang' => Context::getContext()->language->id,
            'designConfiguration' => $this->renderDesingBlocksConfigForm(),
            'dashboardProxyUrl' => Context::getContext()->link->getModuleLink('sd_yourio', 'youradmin'),
        ]);

        $this->output .= $tpl->fetch();

        return $this->output;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitDesignConfigForm')) {
            $form_values = $this->getConfigValues();
            foreach (array_keys($form_values) as $key) {
                Configuration::updateValue($key, Tools::getValue($key));
            }

            $this->output .= $this->displayConfirmation($this->l('Saved with success !'));
        }
    }

    /**
     * Generate selector config form
     */
    public function renderDesingBlocksConfigForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [];
        $this->context->smarty->assign('module_path', _MODULE_DIR_ . $this->name);

        $fieldsForm['form'] = [
            'legend' => [
                'title' => $this->l('Content Blocks Position'),
                'icon' => 'icon-edit',
            ],
            'input' => $this->getInputFields(),
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitDesignConfigForm',
                'icon' => '',
            ],
        ];
        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name .
            '&currentPage=designConfiguration';

        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            ],
        ];

        foreach ($this->getConfigValues() as $key => $value) {
            $helper->fields_value[$key] = $value;
        }

        return $helper->generateForm([$fieldsForm]);
    }

    /**
     * @return array
     */
    public function getConfigValues()
    {
        return [
            ConfigurationHelper::PRODUCT_DESC => Configuration::get(ConfigurationHelper::PRODUCT_DESC),
            ConfigurationHelper::PRODUCT_IMAGES => Configuration::get(ConfigurationHelper::PRODUCT_IMAGES),
            ConfigurationHelper::PRODUCT_BULLETS => Configuration::get(ConfigurationHelper::PRODUCT_BULLETS),
            ConfigurationHelper::PRODUCT_PROS_CONS => Configuration::get(ConfigurationHelper::PRODUCT_PROS_CONS),
            ConfigurationHelper::PRODUCT_QA => Configuration::get(ConfigurationHelper::PRODUCT_QA),
            ConfigurationHelper::PRODUCT_REASON_TO_BUY => Configuration::get(ConfigurationHelper::PRODUCT_REASON_TO_BUY),
            ConfigurationHelper::PRODUCT_REVIEWS => Configuration::get(ConfigurationHelper::PRODUCT_REVIEWS),
            ConfigurationHelper::PRODUCT_SPECIFICATIONS => Configuration::get(ConfigurationHelper::PRODUCT_SPECIFICATIONS),
            ConfigurationHelper::PRODUCT_VIDEO => Configuration::get(ConfigurationHelper::PRODUCT_VIDEO),
            ConfigurationHelper::PRODUCT_PDF => Configuration::get(ConfigurationHelper::PRODUCT_PDF),
        ];
    }

    /**
     * @return array
     */
    public function getInputFields()
    {
        $fields = [
            'PRODUCT_DESC' => 'Product Description',
            'PRODUCT_IMAGES' => 'Product Images',
            'PRODUCT_BULLETS' => 'Product Bullets',
            'PRODUCT_PROS_CONS' => 'Product Pros Cons',
            'PRODUCT_QA' => 'Product QA',
            'PRODUCT_REASON_TO_BUY' => 'Product Reason to Buy',
            'PRODUCT_REVIEWS' => 'Product Reviews',
            'PRODUCT_SPECIFICATIONS' => 'Product Specifications',
            'PRODUCT_VIDEO' => 'Product Videos',
            'PRODUCT_PDF' => 'Product PDF',
        ];

        $inputFields = [];
        foreach ($fields as $key => $label) {
            $inputFields[] = [
                'type' => 'select',
                'label' => $this->trans($label, [], 'Modules.Admin.Config'),
                'name' => constant("ConfigurationHelper::{$key}"),
                'options' => [
                    'query' => $this->formatHooksList(),
                    'id' => 'id_hook',
                    'name' => 'hook_name',
                ],
            ];
        }

        return $inputFields;
    }

    /**
     * @return void
     */
    public function registerHooks()
    {
        $this->registerHook($this->register_hook_list);
    }

    /**
     * @return array
     */
    protected function formatHooksList()
    {
        $hooks = [];
        foreach ($this->hooks_list as $id_hook => $hook_name) {
            $hooks[] = [
                'id_hook' => $id_hook,
                'hook_name' => $hook_name,
            ];
        }

        return $hooks;
    }

    /**
     * @param $hookName
     * @param $params
     *
     * @return string
     */
    protected function handleGenericHook($hookName, $params)
    {
        $config_values = $this->getConfigValues();
        $htmlData = '';
        foreach ($config_values as $key => $value) {
            if ($hookName == 'displayProductPriceBlock') {
                if (isset($params['type']) & str_contains($value, $params['type'])) {
                    $htmlData .= ConfigurationHelper::getContentBlocks()[$key];
                }
            } elseif (strstr($hookName, $value)) {
                $htmlData .= ConfigurationHelper::getContentBlocks()[$key];
            }
        }

        return $htmlData;
    }

    protected function toCamelCase($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9]+/', ' ', $string);
        $string = str_replace(' ', '', ucwords($string));

        return lcfirst($string);
    }

    /**
     * @param $hookName
     * @param array $configuration
     *
     * @return string|void
     */
    public function renderWidget($hookName, array $configuration)
    {
        if (in_array($this->toCamelCase(str_replace('hook', '', $hookName)), $this->register_hook_list)) {
            return $this->handleGenericHook($this->toCamelCase(str_replace('hook', '', $hookName)), $configuration);
        }
    }

    /**
     * @param $hookName
     * @param array $configuration
     *
     * @return void
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
    }
}
