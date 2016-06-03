<?php
/**
 * Instance ID cannot be changed
 *
 * @category    Document
 * @package     Xpressengine\Document
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     LGPL-2.1
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Document\Exceptions;

use Xpressengine\Document\DocumentException;

/**
 * Instance ID cannot be changed
 *
 * @category    Document
 * @package     Xpressengine\Document
 */
class CannotChangeInstanceIdException extends DocumentException
{
    protected $message = 'Instance ID cannot be changed.';
}
