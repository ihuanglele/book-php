<?php
/**
 * Created by PhpStorm.
 * User: ihuanglele
 * Date: 2019/7/4
 * Time: 11:21 PM
 */

namespace app\lib\book\siteImpl;


use app\lib\book\AbstractSite;
use QL\QueryList;

class Qu extends AbstractSite
{

    // https://sou.xanbhx.com/search?siteid=qula&q=%E5%BE%AE%E5%BE%AE%E4%B8%80%E7%AC%91
    const SEARCH_BASE_URI = 'https://sou.xanbhx.com/search?siteid=qula&q=';

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
        return [
            'base_uri' => self::SEARCH_BASE_URI,
            'options' => [
                'query' => [
                    'q' => $key,
                    'siteid' => 'qula',
                ],
                'headers' => self::PC_HEADERS,
                'verify' => false,
            ],
        ];
    }

    protected function _searchTransform($html)
    {
        $Ql = new QueryList();
        $items = $Ql->html($html)->rules([
            'name' => ['#search-main li .s2', 'text'],
            'bookId' => ['#search-main li .s2 a', 'href'],
            'author' => ['#search-main li .s4', 'text'],
        ])->query()->getData();
        $data = [];
        $t = [
            'type' => static::getClassName(),
            'cover' => '',
        ];
        $name = '';
        foreach ($items as $i => $item) {
            if ($i === 0) {
                $name = $item['name'];
                continue;
            }
            if (preg_match('#^https:\/\/www\.qu\.la\/book\/([0-9]*)#', $item['bookId'], $arr)) {
                $t['name'] = $name;
                $t['bookId'] = $arr[1];
                $t['author'] = $item['author'];
                $name = $item['author'];
                $data[] = $t;
            }
        }
        return $data;
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