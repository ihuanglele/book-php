<?php
/**
 * Created by PhpStorm.
 * User: ihuanglele
 * Date: 2019/7/4
 * Time: 11:21 PM
 */

namespace app\lib\book\siteImpl;


use app\lib\book\AbstractSite;
use app\lib\book\BookEntry;
use QL\QueryList;
use function array_filter;
use function array_values;
use function explode;
use function preg_match;
use function sprintf;
use function trim;

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
                                             'name'   => ['.s2', 'text'],
                                             'bookId' => ['.s2 a', 'href'],
                                             'author' => ['.s4', 'text'],
                                         ])->range('#search-main>div>ul>li')->queryData();
        $data = [];
        $t = [
            'type' => static::getClassName(),
            'cover' => '',
        ];

        foreach ($items as $i => $item) {
            if ($i === 0) {
                continue;
            }
            if (preg_match('#^https:\/\/www\.qu\.la\/book\/([0-9]*)#', $item['bookId'], $arr)) {
                $t['name']   = trim($item['name']);
                $t['bookId'] = $arr[1];
                $t['author'] = $item['author'];
                $data[]      = $t;
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
        $html      = $this->getHtml($this->decodeCatUrl($bookId));
        $ql        = QueryList::html($html);
        $bookEntry = new BookEntry();
        $bookEntry->setBookId($bookId);
        $authorVar = explode('：', $ql->find('#info p:eq(0)')->text());
        if (isset($authorVar[1])) {
            $bookEntry->setAuthor($authorVar[1]);
        }
        $bookEntry->setName($ql->find('#info h1:eq(0)')->text());
        $chapters = $ql->rules([
                                   'title' => ['a', 'text'],
                                   'link'  => ['a', 'href'],
                               ])->range('#list dd')->queryData(function($item) use ($bookId)
        {
            $articleId = $this->encodeArticleUrl($item['link']);
            if ($articleId) {
                $item['bookId'] = $bookId;
                $item['link']   = $articleId;
                $item['type']   = $this->getClassName();
                return $item;
            } else {
                return null;
            }
        });
        $bookEntry->setType($this->getClassName());
        $img = $ql->find('#fmimg img')->src;
        if ($img) {
            $bookEntry->setCover(sprintf('https://www.qu.la/%s', $img));
        }
        $bookEntry->setChapters(array_values(array_filter($chapters)));

        return $bookEntry;
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
        $html    = $this->getHtml($this->decodeArticleUrl($articleId, $bookId));
        $ql      = QueryList::html($html);
        $content = $ql->find('#content')->html();
        $content = explode("\n", $content);

        return $this->trim_html($content[0]);
    }

    protected function getCatUrlId($url)
    {
        // TODO: Implement getCatUrlId() method.
    }

    protected function decodeCatUrl($id)
    {
        return sprintf('https://www.qu.la/book/%u/', (int) $id);
    }

    protected function encodeArticleUrl($url)
    {
        // /book/180037/9104487.html
        if (preg_match('#^\/book\/(\d+)/(\d+)\.html$#', $url, $arr)) {
            return $arr[2];
        } else {
            return false;
        }
    }

    protected function decodeArticleUrl($articleId, $bookId = null)
    {
        // https://www.qu.la/book/201052/1116149.html
        return sprintf('https://www.qu.la/book/%u/%u.html', (int) $bookId, (int) $articleId);
    }
}