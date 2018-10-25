<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/24/2018
 * Time: 12:30 AM
 */

namespace MaratMSBootcampPlugin\Controller;


use MaratMSBootcampPlugin\Tools\BootcampBackend;
use MaratMSBootcampPlugin\Tools\Template;

class PublicController
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
     * @var string
     */
    private $backendToken = "";

    /**
     * AdminController constructor.
     * @param string $pluginRootPath
     * @param string $backendUrl
     * @param string $backendToken
     */
    public function __construct($pluginRootPath, $backendUrl, $backendToken)
    {
        $this->pluginRootPath = $pluginRootPath;
        if (preg_match('#^(.*)/+$#', $this->pluginRootPath, $pregResult)) {
            $this->pluginRootPath = $pregResult[1];
        }

        $this->backendUrl = $backendUrl;
        $this->backendToken = $backendToken;
    }

    /**
     * @return string
     */
    public function renderQuoteWidget()
    {
        $backend = new BootcampBackend($this->backendUrl, $this->backendToken);

        $quote = $backend->loadRandomQuote();
        if (! $quote) {
            trigger_error("Couldn't load a random quote!", E_USER_WARNING);
            return "";
        } else {
            return Template::render(
                $this->pluginRootPath . "/public/views/widget.phtml",
                [ "quote" => $quote ]
            );
        }
    }

    /**
     * @param int $authorId
     * @return string
     */
    public function renderAuthorPage($authorId)
    {
        $backend = new BootcampBackend($this->backendUrl, $this->backendToken);

        $quoteList = $backend->loadAuthorQuotes($authorId);
        if (! $quoteList) {
            // trigger_error("No quotes found!", E_USER_WARNING);
            status_header( 404 );
            return "<p>The author not found.</p>";
        } else {
            return Template::render(
                $this->pluginRootPath . "/public/views/author.phtml",
                [ "quoteList" => $quoteList ]
            );
        }
    }
}