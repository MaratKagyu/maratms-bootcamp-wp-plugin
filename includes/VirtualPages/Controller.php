<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/25/2018
 * Time: 10:03 AM
 */

namespace MaratMSBootcampPlugin\VirtualPages;


class Controller implements ControllerInterface
{
    /**
     * @var \SplObjectStorage
     */
    private $pages;

    /**
     * @var TemplateLoaderInterface
     */
    private $loader;

    /**
     * @var
     */
    private $matched;

    /**
     * Controller constructor.
     * @param TemplateLoaderInterface $loader
     */
    function __construct(TemplateLoaderInterface $loader)
    {
        $this->pages = new \SplObjectStorage;
        $this->loader = $loader;
    }

    function init()
    {
        do_action('gm_virtual_pages', $this);
    }

    /**
     * @param PageInterface $page
     * @return PageInterface
     */
    function addPage(PageInterface $page)
    {
        $this->pages->attach($page);
        return $page;
    }

    /**
     * @param bool $bool
     * @param \WP $wp
     * @return bool
     */
    function dispatch($bool, \WP $wp)
    {
        if ($this->checkRequest() && $this->matched instanceof Page) {
            $this->loader->init($this->matched);
            $wp->virtual_page = $this->matched;
            do_action('parse_request', $wp);
            $this->setupQuery();
            do_action('wp', $wp);
            $this->loader->load();
            $this->handleExit();
        }
        return $bool;
    }

    /**
     * @return bool
     */
    private function checkRequest()
    {
        $this->pages->rewind();
        $path = trim($this->getPathInfo(), '/');
        while ($this->pages->valid()) {
            // preg_quote
            $escapedCode = $this->pages->current()->getUrl();

            if (preg_match("#{$escapedCode}#isu", $path)) {
                $this->matched = $this->pages->current();
                return true;
            }
            $this->pages->next();
        }

        return false;
    }

    /**
     * @return string
     */
    private function getPathInfo()
    {
        $home_path = parse_url(home_url(), PHP_URL_PATH);
        return preg_replace("#^/?{$home_path}/#", '/', esc_url(add_query_arg([])));
    }

    /**
     *
     */
    private function setupQuery()
    {
        global $wp_query;
        $wp_query->init();
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->is_home = false;
        $wp_query->found_posts = 1;
        $wp_query->post_count = 1;
        $wp_query->max_num_pages = 1;
        $posts = (array)apply_filters(
            'the_posts', [$this->matched->asWpPost()], $wp_query
        );
        $post = $posts[0];
        $wp_query->posts = $posts;
        $wp_query->post = $post;
        $wp_query->queried_object = $post;
        $GLOBALS['post'] = $post;
        $wp_query->virtual_page =
            $post instanceof \WP_Post && isset($post->is_virtual)
                ? $this->matched
                : null;
    }

    public function handleExit()
    {
        exit();
    }
}