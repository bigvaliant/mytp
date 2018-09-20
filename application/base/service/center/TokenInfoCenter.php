<?php
/**
 * Created by PhpStorm.
 * User: ÎÒµÄµçÄÔ
 * Date: 2018/9/5
 * Time: 16:46
 */

namespace app\base\service\center;

use app\base\service\base\InfoBase;
class TokenInfoCenter extends InfoBase
{
    public function _initialize()
    {
        $this->infoString = OptionsCenter::$infoToken;
    }

}