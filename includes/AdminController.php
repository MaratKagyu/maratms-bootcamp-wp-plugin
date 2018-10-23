<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/23/2018
 * Time: 9:06 PM
 */

namespace MaratMSBootcampPlugin;

use MaratMSBootcampPlugin\Entity\Quote;
use MaratMSBootcampPlugin\Tools\BootcampBackend;
use MaratMSBootcampPlugin\Tools\Template;
use MaratMSBootcampPlugin\Tools\WpUrlGenerator;

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
    public function renderQuoteListPage()
    {
        $backend = new BootcampBackend($this->backendUrl, $this->backendToken);
        $quoteList = $backend->loadQuoteList();

        return Template::render(
            $this->pluginRootPath . "/admin/views/quote-list-page.phtml",
            [
                "quoteList" => $quoteList
            ]
        );
    }


    /**
     * @param int $quoteId
     * @return string
     */
    public function renderQuoteEditPage($quoteId)
    {
        $quoteId = (int)$quoteId;

        if ($quoteId) {
            $backend = new BootcampBackend($this->backendUrl, $this->backendToken);
            $quote = $backend->loadQuote($quoteId);
            if (! $quote) {
                status_header( 404 );
                nocache_headers();
                include( get_404_template() );
                die();
            }
        } else {
            $quote = new Quote();
        }

        return Template::render(
            $this->pluginRootPath . "/admin/views/quote-edit-page.phtml",
            [
                "quote" => $quote
            ]
        );
    }

    /**
     * @param int $quoteId - if === 0, then it add a new quote
     * @param string $authorName
     * @param string $quoteText
     */
    public function saveQuoteAction($quoteId, $authorName, $quoteText)
    {
        $backend = new BootcampBackend($this->backendUrl, $this->backendToken);
        $backend->saveQuote($quoteId, $authorName, $quoteText);

        wp_redirect( WpUrlGenerator::getQuoteListPageUrl() );
    }

    /**
     * @param int $quoteId
     */
    public function deleteQuoteAction($quoteId)
    {
        $backend = new BootcampBackend($this->backendUrl, $this->backendToken);
        $backend->deleteQuote($quoteId);

        wp_redirect( WpUrlGenerator::getQuoteListPageUrl() );
    }
}