<?php

namespace MaratMSBootcampPlugin;

use MaratMSBootcampPlugin\Tools\Template;

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
    public function register()
    {
        $this->addAdminScripts();
        $this->addClientScripts();
        $this->addAdminMenu();
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
    private function addAdminMenu()
    {
        add_action('admin_menu', function () {
            add_menu_page(
                'Bootcamp quotes',
                'Bootcamp',
                'manage_options',
                'maratms-bootcamp',
                function () {
                    print($this->getAdminController()->renderQuoteListPage());
                },
                "",
                5
            );
        });
    }

    /**
     * @return AdminController
     */
    private function getAdminController()
    {
        return new AdminController($this->pluginRoot, $this->backendUrl);
    }
}
