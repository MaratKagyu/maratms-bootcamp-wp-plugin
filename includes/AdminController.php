<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/23/2018
 * Time: 9:06 PM
 */

namespace MaratMSBootcampPlugin;

use MaratMSBootcampPlugin\Tools\Template;

class AdminController
{
    /**
     * @var string
     */
    private $pluginRootPath = "";

    /**
     * @var string
     */
    private $backendUrl = "";

    /**
     * AdminController constructor.
     * @param $pluginRootPath
     * @param $backendUrl
     */
    public function __construct($pluginRootPath, $backendUrl)
    {
        $this->pluginRootPath = $pluginRootPath;
        if (preg_match('#^(.*)/+$#', $this->pluginRootPath, $pregResult)) {
            $this->pluginRootPath = $pregResult[1];
        }

        $this->backendUrl = $backendUrl;
    }

    /**
     * @return string
     */
    public function renderQuoteListPage()
    {
        return Template::render(
            $this->pluginRootPath . "/admin/views/quote-list-page.phtml",
            []
        );
    }
}