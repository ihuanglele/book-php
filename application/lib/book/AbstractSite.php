<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 17:14
 */

namespace app\lib\book;


abstract class AbstractSite
{

    abstract public function search($k, $p);

    /**
     * 搜索
     * @param $key
     * @param $p
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    abstract public function searchUrl($key, $p);

    abstract public function searchTransform($key, $p);

    /**
     * 获取目录
     * @param $bookId
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    abstract public function getCat($bookId);

    /**
     * 获取正文
     * @param $bookId
     * @param $article
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    abstract public function getArticle($bookId, $article);

}