<?php
/**
 * Created by PhpStorm.
 * User: нр╣д╣Гдт
 * Date: 2018/9/6
 * Time: 9:50
 */

namespace app\base\model;

use app\base\model\base\BaseHash;

class TestHash extends BaseHash
{
    protected $table="my_test";

    protected $autoWriteTimestamp = false;
    protected $hashKey="id";
}