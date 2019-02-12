<?php
/**
 * Created by PhpStorm.
 * User: ihuanglele
 * Date: 2019/2/4
 * Time: 6:07 PM
 */

namespace app\lib\book\siteImpl;


use app\lib\book\AbstractSite;

class Yznnw extends AbstractSite
{

    /**
     * @param $key
     * @param $p
     * @return array
     * [
     *      'base_uri'  => '',
     *      'options'   => []
     * ]
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-22
     */
    protected function buildSearchConf($key, $p)
    {
        // TODO: Implement buildSearchConf() method.
    }

    protected function _searchTransform($html)
    {
        // TODO: Implement _searchTransform() method.
    }

    /**
     * 获取目录
     * @param $bookId
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    public function getCat($bookId)
    {
        // TODO: Implement getCat() method.
    }

    /**
     * 获取正文
     * @param $articleId
     * @param $bookId
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    public function getArticle($articleId, $bookId)
    {
        // TODO: Implement getArticle() method.
    }

    protected function getCatUrlId($url)
    {
        // TODO: Implement getCatUrlId() method.
    }

    protected function decodeCatUrl($id)
    {
        // TODO: Implement decodeCatUrl() method.
    }

    protected function encodeArticleUrl($url)
    {
        // TODO: Implement encodeArticleUrl() method.
    }

    protected function decodeArticleUrl($articleId, $bookId = null)
    {
        // TODO: Implement decodeArticleUrl() method.
    }
}