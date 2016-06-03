<?php
/**
 * SkinNotFoundException class. This file is part of the Xpressengine package.
 *
 * @category    Skin
 * @package     Xpressengine\Skin
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     LGPL-2.1
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Skin\Exceptions;

use Xpressengine\Skin\SkinException;

/**
 * 스킨을 찾을 수 없을 경우 발생하는 예외이다.
 *
 * @category    Skin
 * @package     Xpressengine\Skin
 */
class SkinNotFoundException extends SkinException
{
    protected $message = 'Skin을 찾을 수 없습니다';
}
