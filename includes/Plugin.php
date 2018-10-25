<?php

namespace MaratMSBootcampPlugin;

use MaratMSBootcampPlugin\Controller\AdminController;
use MaratMSBootcampPlugin\Controller\PublicController;
use MaratMSBootcampPlugin\Tools\BootcampBackend;
use MaratMSBootcampPlugin\Tools\WpUrlGenerator;
use MaratMSBootcampPlugin\VirtualPages\Controller;
use MaratMSBootcampPlugin\VirtualPages\ControllerInterface;
use MaratMSBootcampPlugin\VirtualPages\Page;
use MaratMSBootcampPlugin\VirtualPages\TemplateLoader;

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
     * @var string
     */
    private $appToken = "";

    /**
     * Plugin constructor.
     * @param $pluginRoot
     */
    public function __construct($pluginRoot)
    {
        $this->pluginRoot = $pluginRoot;
        $this->appToken = base64_encode(AUTH_KEY);
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

        $this->addPublicScripts();
        $this->enablePublicControllers();
        $this->registerPublicRoutes();
    }

    /**
     * @throws Exception\BootcampException
     */
    public function activate()
    {
        // Register the app
        $backend = new BootcampBackend($this->backendUrl, $this->appToken);
        $backend->register();
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
    private function addPublicScripts()
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
        add_action('admin_enqueue_scripts', function () {
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
    private function enablePublicControllers()
    {
        $controller = new Controller(new TemplateLoader());

        add_action('init', function () use ($controller) {
            $controller->init();
        });

        add_filter('do_parse_request', [$controller, 'dispatch'], PHP_INT_MAX, 2);

        add_action('loop_end', function (\WP_Query $query) {
            if (isset($query->virtual_page) && !empty($query->virtual_page)) {
                $query->virtual_page = null;
            }
        });

        add_filter('the_permalink', function ($pLink) {
            global $post, $wp_query;
            if (
                $wp_query->is_page && isset($wp_query->virtual_page)
                && $wp_query->virtual_page instanceof Page
                && isset($post->is_virtual) && $post->is_virtual
            ) {
                $pLink = home_url($wp_query->virtual_page->getUrl());
            }
            return $pLink;
        });
    }

    /**
     *
     */
    private function registerPublicRoutes()
    {
        // Widget
        add_action('wp_footer', function () {
            print($this->getPublicController()->renderQuoteWidget());
        });

        // Quotes by the author
        add_action('gm_virtual_pages', function (ControllerInterface $controller) {
            $controller->addPage(new Page('bootcamp\/\?authorId=\d+'))
                ->setTitle('Bootcamp quotes')
                ->setContentGenerator(function () {
                    $authorId = isset($_GET['authorId']) ? (int)$_GET['authorId'] : 0;
                    return $this->getPublicController()->renderAuthorPage($authorId);
                })
            ;
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
                ->saveQuoteAction($quoteId, $authorName, $quoteText);
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
        return new AdminController($this->pluginRoot, $this->backendUrl, $this->appToken);
    }

    /**
     * @return PublicController
     */
    private function getPublicController()
    {
        return new PublicController($this->pluginRoot, $this->backendUrl, $this->appToken);
    }

}
