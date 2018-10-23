<?php

namespace MaratMSBootcampPlugin;

use MaratMSBootcampPlugin\Tools\WpUrlGenerator;

class Plugin
{
    /**
     * @var string
     */
    private $pluginRoot = '';

    /**
     * @var string
     */
    private $backendUrl = '';

    /**
     * Plugin constructor.
     * @param $pluginRoot
     */
    public function __construct($pluginRoot)
    {
        $this->pluginRoot = $pluginRoot;
        $this->loadConfig();
    }

    /**
     * Loads config from:
     * config/plugin-config.ini
     * OR
     * config/plugin-config.ini.dist
     */
    private function loadConfig()
    {
        $configFilesOrder = [
            $this->pluginRoot . "config/plugin-config.ini",
            $this->pluginRoot . "config/plugin-config.ini.dist",
        ];

        foreach ($configFilesOrder as $filePath) {
            if (file_exists($filePath)) {
                $configData = parse_ini_file($filePath);
                $this->backendUrl = isset($configData['backend_url']) ? $configData['backend_url'] : '';

                // Remove trailing the slash, if it exists
                if (preg_match('#^(.*)/+$#', $this->backendUrl, $pregResult)) {
                    $this->backendUrl = $pregResult[1];
                }
                break;
            }
        }
    }

    /**
     *
     */
    public function init()
    {
        $this->addAdminScripts();
        $this->registerAdminRoutes();

        $this->addClientScripts();
    }

    /**
     *
     */
    public function activate()
    {

    }

    /**
     *
     */
    public function deactivate()
    {

    }

    /**
     *
     */
    public static function uninstall()
    {

    }

    /**
     *
     */
    private function addClientScripts()
    {
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_style(
                'maratms-bootcamp-plugin-public-style',
                plugins_url('/public/css/style.css', $this->pluginRoot . "maratms-bootcamp-plugin.php")
            );
        });
    }

    /**
     *
     */
    private function addAdminScripts()
    {
        add_action('admin_enqueue_scripts', function (){
            // Styles
            wp_enqueue_style(
                'maratms-bootcamp-plugin-admin-style',
                plugins_url('/admin/css/style.css', $this->pluginRoot . "maratms-bootcamp-plugin.php")
            );
        });
    }

    /**
     *
     */
    private function registerAdminRoutes()
    {
        // Quote list page
        add_action('admin_menu', function () {
            add_menu_page(
                'Bootcamp quotes',
                'Bootcamp',
                'manage_options',
                WpUrlGenerator::PAGE_QUOTE_LIST_SLUG,
                function () {
                    print($this->getAdminController()->renderQuoteListPage());
                },
                "",
                5
            );
        });

        // Quote edit page
        add_action('admin_menu', function () {
            add_submenu_page(
                null,
                "Edit quote",
                null,
                "manage_options",
                WpUrlGenerator::PAGE_QUOTE_EDIT_SLUG,
                function () {
                    $quoteId = isset($_GET['quoteId']) ? $_GET['quoteId'] : 0;
                    print($this->getAdminController()->renderQuoteEditPage($quoteId));
                }
            );
        });

        // Quote save action
        add_action('admin_action_' . WpUrlGenerator::ACTION_QUOTE_SAVE_SLUG, function () {
            $quoteId = isset($_GET['quoteId']) ? $_GET['quoteId'] : 0;
            $authorName = isset($_POST['authorName']) ? $_POST['authorName'] : "";
            $quoteText = isset($_POST['quoteText']) ? $_POST['quoteText'] : "";
            $this
                ->getAdminController()
                ->saveQuoteAction($quoteId, $authorName, $quoteText)
            ;
        });

        // Quote delete action
        add_action('admin_action_' . WpUrlGenerator::ACTION_QUOTE_DELETE_SLUG, function () {
            $quoteId = isset($_GET['quoteId']) ? $_GET['quoteId'] : 0;
            $this->getAdminController()->deleteQuoteAction($quoteId);
        });
    }

    /**
     * @return AdminController
     */
    private function getAdminController()
    {
        return new AdminController($this->pluginRoot, $this->backendUrl, "");
    }
}
