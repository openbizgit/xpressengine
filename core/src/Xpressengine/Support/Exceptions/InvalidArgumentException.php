<?php
/**
 *  AccessDeniedHttpException Class
 *
 * @category    Exceptions
 * @package     Xpressengine\Support\Exceptions
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     LGPL-2.1
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Support\Exceptions;

/**
 * InvalidArgumentException
 *
 * @category    Exceptions
 * @package     Xpressengine\Support\Exceptions
 */
class InvalidArgumentException extends XpressengineException
{
    /**
     * @var string exception code
     */
    protected $message = '잘못된 요청입니다.';
}
