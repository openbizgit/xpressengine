<?php
/**
 * This file is search engine optimizer usable interface.
 *
 * @category    Seo
 * @package     Xpressengine\Seo
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     LGPL-2.1
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Seo;

/**
 * SeoUsable interface
 *
 * @category    Seo
 * @package     Xpressengine\Seo
 */
interface SeoUsable
{
    /**
     * Returns title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns keyword
     *
     * @return string|array
     */
    public function getKeyword();

    /**
     * Returns url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Returns author
     *
     * @return string
     */
    public function getAuthor();

    /**
     * Returns image url list
     *
     * @return array
     */
    public function getImages();
}
