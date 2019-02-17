<?php
/**
 * Created by PhpStorm.
 * User: ihuanglele
 * Date: 2019/2/4
 * Time: 6:07 PM
 */

namespace app\lib\book\siteImpl;


use app\lib\book\AbstractSite;
use app\lib\book\BookEntry;
use QL\QueryList;

class Yznnw extends AbstractSite
{

    const URL = 'http://m.yznnw.com/index.php';

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
            'base_uri' => self::URL . '/book/store/',
            'options' => [
                'query' => [
                    'searchkey' => $key,
                    'page' => $p - 1,
                    'ajaxMethod' => 'getsearchbooks',
                ],
                'headers' => self::PC_HEADERS + ['X-Requested-With' => 'XMLHttpRequest'],
            ],
        ];
    }

    protected function _searchTransform($html)
    {
        $data = json_decode($html, true);
        if (!$data || !isset($data['Flag']) || !$data['Flag']
            || !isset($data['Data']['search_response']['books']) || empty($data['Data']['search_response']['books'])) {
            return [];
        }
        $arr = [];
        foreach ($data['Data']['search_response']['books'] as $book) {
            $arr[] = [
                'name' => $book['bookname'],
                'type' => static::getClassName(),
                'bookId' => $book['bookid'],
                'author' => $book['authorname'],
                'cover' => $book['coverurl'],
            ];
        }
        return $arr;
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
        $infoUrl = 'http://m.yznnw.com/index.php/book/cover/bid=' . $bookId . '/';
        $html = file_get_contents($infoUrl);
        $ql = QueryList::html($html);
        $bookEntry = new BookEntry();
        $bookEntry->setBookId($bookId);
        $name = $ql->find('.name:eq(0) a')->text();
        $bookEntry->setName(rtrim($name, '全文阅读'));
        $bookEntry->setAuthor($ql->find('.dd_box:eq(0) a')->text());
        $bookEntry->setCover($ql->find('.pic img:eq(0)')->src);
        return $bookEntry->toArray();
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