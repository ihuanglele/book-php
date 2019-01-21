<?php

use app\lib\book\Book;

/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 16:03
 */

class IndexController extends BaseController
{

    public function indexAction()
    {
        $this->success('ok');
    }

    public function searchAction()
    {
        $key = $this->getParam('key', '');
        $p   = $this->getParam('p', 1);
        Book::search($key, $p);
    }



}