<?php
/**
 * Created by PhpStorm.
 * Author: ihuanglele<huanglele@yousuowei.cn>
 * Date: 2019-01-21
 * Time: 17:29
 */

namespace app\lib\book\siteImpl;


use app\lib\book\AbstractSite;
use app\lib\book\ArticleEntry;
use app\lib\book\BookEntry;
use QL\Dom\Elements;
use QL\QueryList;
use function explode;
use function preg_match;
use function str_replace;
use function trim;

class Qisuu extends AbstractSite
{

    const SITE = 'https://www.qisuu.la/';

    // http://zhannei.baidu.com/cse/site/site?q=key&p=3&cc=qisuu.la
    const SEARCH_BASE_URI = 'http://zhannei.baidu.com/cse/site/';

    /**
     * 获取目录
     * @param $bookId
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    public function getCat($bookId)
    {
        $html      = $this->getHtml($this->decodeCatUrl($bookId));
        $ql        = QueryList::html($html);
        $bookEntry = new BookEntry();
        $bookEntry->setBookId($bookId);
        $bookEntry->setName($ql->find('.info_des h1:eq(0)')->text());
        $author = $ql->find('.info_des dl:eq(0)')->text();
        if ($author) {
            list($t1, $t2) = explode('：', $author);
            $author = trim($t2);
        } else {
            $author = '';
        }
        $bookEntry->setAuthor($author);
        $bookEntry->setType($this->getClassName());
        $img = $ql->find('.tupian img:eq(0)')->src;
        if ($img) {
            $bookEntry->setCover('https://www.qisuu.la'.$img);
        }
        $outLineArr = explode('<br>', $ql->find('.intro')->htmls()[0]);
        $bookEntry->setOutline($this->trim_html($outLineArr[0]));
        $ql->find('.pc_list:eq(1) li')->map(function($item) use ($bookEntry)
        {
            $articleEntry = new ArticleEntry();
            $articleEntry->setBookId($bookEntry->getBookId());
            $articleEntry->setType($bookEntry->getType());
            $articleEntry->setArticleId($this->encodeArticleUrl($item->find('a:eq(0)')->href));
            $articleEntry->setTitle($item->text());
            $bookEntry->addChapter($articleEntry);
        });

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
        $ql = QueryList::html($this->getHtml($this->decodeArticleUrl($articleId, $bookId)));

        return $ql->find('#content1')->text();
    }

    /**
     * 搜索
     * @param $key
     * @param $p
     * @return array
     * @author ihuanglele<huanglele@yousuowei.cn>
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
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-22
     */
    protected function _searchTransform($html)
    {
        return QueryList::html($html)->find('.c-title')->map(function($item)
        {
            /**
             * @var $item Elements
             */
            $title = $item->text();
            if (preg_match('/^(.*?)\((.*?)\)最新章节/', $title, $arr)) {
                $link = $item->find('a')->attr('href');

                return [
                    'name'   => $arr[1],
                    'type'   => static::getClassName(),
                    'bookId' => $this->getCatUrlId($link),
                    'author' => $arr[2],
                    'cover'  => '',
                ];
            }
        });
    }

    protected function getCatUrlId($url)
    {
        list($r, $p) = explode('du/', $url);
        $arr = explode('/', $p);

        return $arr[0].'-'.$arr[1];
    }

    protected function decodeCatUrl($id)
    {
        return self::SITE.'du/'.str_replace('-', '/', $id).'/';
    }

    protected function encodeArticleUrl($url)
    {
        if ($url) {
            return substr($url, 0, -5);
        } else {
            return '';
        }
    }

    protected function decodeArticleUrl($articleId, $bookId = null)
    {
        if (empty($bookId)) {
            $bookId = '1/1';
        } else {
            $bookId = str_replace('-', '/', $bookId);
        }

        return self::SITE.'du/'.$bookId.'/'.$articleId.'.html';
    }
}