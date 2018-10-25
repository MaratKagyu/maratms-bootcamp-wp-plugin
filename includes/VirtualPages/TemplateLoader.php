<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/25/2018
 * Time: 10:07 AM
 */

namespace MaratMSBootcampPlugin\VirtualPages;


class TemplateLoader implements TemplateLoaderInterface
{
    /**
     * @var array
     */
    private $templates = [];

    /**
     * @param PageInterface $page
     */
    public function init(PageInterface $page)
    {
        $this->templates = wp_parse_args(
            ['page.php', 'index.php'], (array)$page->getTemplate()
        );
    }

    /**
     *
     */
    public function load()
    {
        do_action('template_redirect');
        $template = locate_template(array_filter($this->templates));
        $filtered = apply_filters('template_include',
            apply_filters('virtual_page_template', $template)
        );
        if (empty($filtered) || file_exists($filtered)) {
            $template = $filtered;
        }
        if (!empty($template) && file_exists($template)) {
            require_once $template;
        }
    }
}