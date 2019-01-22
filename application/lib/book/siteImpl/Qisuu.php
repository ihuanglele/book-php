<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 17:29
 */

namespace app\lib\book\siteImpl;


use app\lib\book\AbstractSite;
use QL\Dom\Elements;
use QL\QueryList;
use function explode;
use function preg_match;

class Qisuu extends AbstractSite
{

    const SITE = 'https://www.qisuu.la/';

    // http://zhannei.baidu.com/cse/site/site?q=key&p=3&cc=qisuu.la
    const SEARCH_BASE_URI = 'http://zhannei.baidu.com/cse/site/';

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
     * @return array
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    protected function buildSearchConf($key, $p)
    {
        return [
            'base_uri' => self::SEARCH_BASE_URI,
            'options'  => [
                'query'   => [
                    'q'  => $key,
                    'p'  => $p - 1,
                    'cc' => 'qisuu.la',
                ],
                'headers' => self::PC_HEADERS,
            ],
        ];
    }

    /**
     * 过滤匹配搜索结果
     * @param $html
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-22
     */
    public function searchTransform($html)
    {
        $list = [];
        $this->saveText(static::class.'search_list', $html);
        try {
            QueryList::html($html)->find('.c-title')->map(function($item) use (&$list)
            {
                /**
                 * @var $item Elements
                 */
                $title  = $item->text();
                $author = '';
                $img    = '';
                $link   = $item->find('a')->attr('href');
                if (preg_match('/^(.*?)\((.*?)\)最新章节/', $title, $arr)) {
                    $title  = $arr[1];
                    $author = $arr[2];
                }
                $list[] = [
                    'title'  => $title,
                    'link'   => $this->encodeCatUrl($link),
                    'author' => $author,
                    'img'    => $img,
                ];
            });
        } catch (\Exception $e) {

        } finally {
            return $list;
        }
    }

    protected function encodeCatUrl($url)
    {
        // TODO: Implement encodeCatUrl() method.
        list($r, $p) = explode('du/', $url);
        $arr = explode('/', $p);

        return [
            'type'   => $this->getClassName(),
            'params' => $arr[0].'-'.$arr[1],
        ];
    }

    protected function decodeCatUrl($params)
    {
        // TODO: Implement decodeCatUrl() method.
    }

    protected function encodeArticleUrl($url)
    {
        // TODO: Implement encodeArticleUrl() method.
    }

    protected function decodeArticleUrl($params)
    {
        // TODO: Implement decodeArticleUrl() method.
    }
}