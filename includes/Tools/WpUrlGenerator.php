<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/23/2018
 * Time: 10:28 PM
 */

namespace MaratMSBootcampPlugin\Tools;


class WpUrlGenerator
{
    const PAGE_QUOTE_LIST_SLUG = 'bootcamp-quote-list';
    const PAGE_QUOTE_EDIT_SLUG = 'bootcamp-quote-edit';
    const ACTION_QUOTE_SAVE_SLUG = 'bootcamp-quote-save';
    const ACTION_QUOTE_DELETE_SLUG = 'bootcamp-quote-delete';


    /**
     * @return string
     */
    public static function getQuoteListPageUrl()
    {
        return admin_url('admin.php') . "?page=" . self::PAGE_QUOTE_LIST_SLUG;
    }

    /**
     * @param int $quoteId
     * @return string
     */
    public static function getQuoteEditPageUrl($quoteId = 0)
    {
        $quoteId = (int)$quoteId;
        return admin_url('admin.php') . "?page=" . self::PAGE_QUOTE_EDIT_SLUG . "&quoteId=" . $quoteId;
    }

    /**
     * @param int $quoteId
     * @return string
     */
    public static function getSaveQuoteActionUrl($quoteId)
    {
        $quoteId = (int)$quoteId;
        return admin_url('admin.php') . "?action=" . self::ACTION_QUOTE_SAVE_SLUG . "&quoteId=" . $quoteId;
    }


    /**
     * @param int $quoteId
     * @return string
     */
    public static function getDeleteQuoteActionUrl($quoteId)
    {
        $quoteId = (int)$quoteId;
        return admin_url('admin.php') . "?action=" . self::ACTION_QUOTE_DELETE_SLUG . "&quoteId=" . $quoteId;
    }

    /**
     * @param int $authorId
     * @return string
     */
    public static function getAuthorPageUrl($authorId)
    {
        $authorId = (int)$authorId;
        return home_url('/bootcamp/') . "?authorId=" . $authorId;
    }
}