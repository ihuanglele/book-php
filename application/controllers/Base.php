<?php

use fw\exception\ResponseException;

/**
 * Created by PhpStorm.
 * Author: ihuanglele<huanglele@yousuowei.cn>
 * Date: 2019-01-21
 * Time: 16:03
 */


class BaseController extends \fw\Controller
{

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if (!CLI) {
            if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
                throw new ResponseException();
            }
        }
    }

}