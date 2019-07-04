<?php

use app\lib\book\Book;

/**
 * Created by PhpStorm.
 * Author: ihuanglele<huanglele@yousuowei.cn>
 * Date: 2019-01-21
 * Time: 16:03
 */

class IndexController extends BaseController
{

    public function indexAction()
    {
        $this->success([
                           'get'  => $this->get(),
                           'post' => $this->post(),
                       ]);
    }

    public function searchAction()
    {
        $key = $this->get('key', '');
        $p = $this->get('p', 1);
        $this->success(Book::search($key, $p));
    }

    public function sAction()
    {
        $key = $this->getRequest()->getParam('key', '');
        $p = $this->getRequest()->getParam('p', 1);
        $this->success(Book::search($key, $p));
    }

    public function catAction()
    {
        $type   = $this->get('type');
        $bookId = $this->get('bookId');
        $data   = Book::cat($type, $bookId);
        $this->success($data);
    }

    public function articleAction()
    {
        $type      = $this->get('type');
        $bookId    = $this->get('bookId');
        $articleId = $this->get('articleId');
        $this->success(Book::article($type, $bookId, $articleId));
    }

}