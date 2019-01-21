<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 17:29
 */

namespace app\lib\book\Siteimpl;


use app\lib\book\AbstractSite;
use GuzzleHttp\Client;

class Qisuu extends AbstractSite
{

    const SITE = 'https://www.qisuu.la/';

    // http://zhannei.baidu.com/cse/site/site?q=key&p=3&cc=qisuu.la
    const SEARCH_HOST = 'http://zhannei.baidu.com/cse/site/';

    /**
     * 搜索
     * @param $key
     * @param $p
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    public function search($key, $p)
    {
        $client = new Client([
                                 'base_uri' => Qisuu::SEARCH_HOST,
                             ]);

        return $client->getAsync('&q='.$key.'&p='.($p - 1));
    }

    /**
     * 获取目录
     * @param $bookId
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    public function getCat($bookId)
    {
        // TODO: Implement getCat() method.
    }

    /**
     * 获取正文
     * @param $bookId
     * @param $article
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    public function getArticle($bookId, $article)
    {
        // TODO: Implement getArticle() method.
    }

    /**
     * 搜索
     * @param $key
     * @param $p
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    public function searchUrl($key, $p)
    {
        // TODO: Implement searchUrl() method.
    }

    public function searchTransform($key, $p)
    {
        // TODO: Implement searchTransform() method.
    }
}