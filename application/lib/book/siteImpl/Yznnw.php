<?php
/**
 * Created by PhpStorm.
 * User: ihuanglele
 * Date: 2019/2/4
 * Time: 6:07 PM
 */

namespace app\lib\book\siteImpl;


use app\lib\book\AbstractSite;
use app\lib\book\ArticleEntry;
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
        $infoUrl = "http://www.yznnw.com/txt/$bookId.html";
        $html = file_get_contents($infoUrl);
        $html = $this->toUtf8($html);
        $ql = QueryList::html($html);
        $bookEntry = new BookEntry();
        $bookEntry->setBookId($bookId);
        $bookEntry->setName($ql->find('.src a:last')->text());
        $bookEntry->setAuthor($ql->find('#bookname:eq(0) a')->text());
        $bookEntry->setCover($ql->find('.coverbox img:eq(0)')->src);
        $outline = $ql->find('.introtxt')->text();
        $bookEntry->setType($this->getClassName());
        $outline = $this->trim_html($outline);
        $bookEntry->setOutline($outline);
        $catUrl = $ql->find('.opendir a:first')->attr('href');
        if ($catUrl) {
//            $catHtml = $this->toUtf8(file_get_contents($catUrl));
            $header = self::PC_HEADERS;
            $header['Host'] = 'www.yznnw.com';
            $catHtml = $this->toUtf8($this->getHtml($catUrl), [
                'header' => $header,
            ]);
            unset($ql);
            $catFirstId = 0;
            if (preg_match('/html\/(\d)\//', $catUrl, $arr)) {
                $catFirstId = $arr[1];
            }
            $ql = QueryList::html($catHtml);
            $ql->find('.zjlist4 li')->map(function ($item) use ($bookEntry, $catFirstId) {
                $articleEntry = new ArticleEntry();
                $articleEntry->setBookId($bookEntry->getBookId());
                $articleEntry->setType($bookEntry->getType());
                $articleId = rtrim($item->find('a:eq(0)')->href, '.html');
                $articleEntry->setArticleId($catFirstId . '-' . $articleId);
                $title = $item->text();
                $title = preg_replace('/^(Part)(\s?)(\d)(\s?)/', '', $title);
                $articleEntry->setTitle($title);
                $bookEntry->addChapter($articleEntry);
            });
        }
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
        $url = $this->decodeArticleUrl($articleId, $bookId);
        $html = $this->toUtf8($this->getHtml($url));
        $ql = QueryList::html($html);
        $content = $ql->find('#htmlContent')->text();
        $content = ltrim($content, '(全本小说网?www.yznnw.com\\|m.yznnw.coＭ\\?)');
        return $content;
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
        // http://www.yznnw.com/files/article/html/3/3645/1894933.html
        list($f, $id) = explode('-', $articleId);
        return "http://www.yznnw.com/files/article/html/$f/$bookId/$id.html";
    }
}