<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/25/2018
 * Time: 9:53 AM
 */

namespace MaratMSBootcampPlugin\VirtualPages;


interface PageInterface
{
    /**
     * @return string
     */
    function getUrl();

    /**
     * @return string
     */
    function getTemplate();

    /**
     * @return string
     */
    function getTitle();

    /**
     * @param string $title
     * @return static
     */
    function setTitle($title);

    /**
     * @param string $title
     * @return static
     */
    function setContentGenerator($title);

    /**
     * @param string $template
     * @return static
     */
    function setTemplate($template);

    /**
     * Get a WP_Post build using virtual Page object
     *
     * @return \WP_Post
     */
    function asWpPost();
}