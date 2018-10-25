<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/25/2018
 * Time: 9:54 AM
 */

namespace MaratMSBootcampPlugin\VirtualPages;


interface TemplateLoaderInterface
{
    /**
     * Setup loader for a page objects
     *
     * @param PageInterface $page matched virtual page
     */
    public function init(PageInterface $page);

    /**
     * Trigger core and custom hooks to filter templates,
     * then load the found template.
     */
    public function load();
}