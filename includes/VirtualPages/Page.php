<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/25/2018
 * Time: 9:55 AM
 */

namespace MaratMSBootcampPlugin\VirtualPages;


class Page implements PageInterface
{
    /**
     * @var string
     */
    private $url = "";

    /**
     * @var string
     */
    private $title = "";

    /**
     * @var callable
     */
    private $contentGenerator;

    /**
     * @var string
     */
    private $template = "";

    /**
     * @var \WP_Post
     */
    private $wpPost;

    /**
     * Page constructor.
     * @param string $url
     * @param string $title
     * @param string $template
     */
    function __construct($url, $title = 'Untitled', $template = 'page.php')
    {
        $this->url = filter_var($url, FILTER_SANITIZE_URL);
        $this->setTitle($title);
        $this->setTemplate($template);
    }

    /**
     * @return string
     */
    function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return static
     */
    function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return static
     */
    function setTitle($title)
    {
        $this->title = filter_var($title, FILTER_SANITIZE_STRING);
        return $this;
    }

    /**
     * @return callable
     */
    public function getContentGenerator()
    {
        return $this->contentGenerator;
    }

    /**
     * @param callable $contentGenerator
     * @return Page
     */
    public function setContentGenerator($contentGenerator)
    {
        $this->contentGenerator = $contentGenerator;
        return $this;
    }

    /**
     * @return string
     */
    function getContent()
    {
        return $this->getContentGenerator()();
    }

    /**
     * @return \WP_Post
     */
    function asWpPost()
    {
        if (is_null($this->wpPost)) {
            $post = [
                'ID' => 0,
                'post_title' => $this->getTitle(),
                'post_name' => sanitize_title($this->getTitle()),
                'post_content' => $this->getContent(),
                'post_excerpt' => '',
                'post_parent' => 0,
                'menu_order' => 0,
                'post_type' => 'page',
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'comment_count' => 0,
                'post_password' => '',
                'to_ping' => '',
                'pinged' => '',
                'guid' => home_url($this->getUrl()),
                'post_date' => current_time('mysql'),
                'post_date_gmt' => current_time('mysql', 1),
                'post_author' => is_user_logged_in() ? get_current_user_id() : 0,
                'is_virtual' => true,
                'filter' => 'raw'
            ];
            $this->wpPost = new \WP_Post((object)$post);
        }
        return $this->wpPost;
    }
}