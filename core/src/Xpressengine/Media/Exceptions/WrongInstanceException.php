<?php
/**
 * This file is instance not match exception
 *
 * @category    Media
 * @package     Xpressengine\Media
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     LGPL-2.1
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Media\Exceptions;

use Xpressengine\Media\MediaException;

/**
 * instance 가 적절하지 않은 경우
 *
 * @category    Media
 * @package     Xpressengine\Media
 */
class WrongInstanceException extends MediaException
{
    protected $message = '잘못된 instance 입니다.';
}
