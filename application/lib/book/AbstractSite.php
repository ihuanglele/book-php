<?php
/**
 * Created by PhpStorm.
 * Author: ihuanglele<huanglele@yousuowei.cn>
 * Date: 2019-01-21
 * Time: 17:14
 */

namespace app\lib\book;

use ArrayAccess;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use InvalidArgumentException;
use Yaf\Exception;
use function array_filter;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function str_replace;
use function strip_tags;
use function strrchr;
use function var_dump;
use const ROOT_PATH;

abstract class AbstractSite
{

    const PC_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36';
    const ACCEPT   = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';

    public $searchCookieKey  = 'search';
    public $catCookieKey     = 'cat';
    public $articleCookieKey = 'article';

    const PC_HEADERS = [
        'User-Agent' => self::PC_AGENT,
        'Accept'     => self::ACCEPT,
        'Accept-Language' => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7'
    ];

    protected $className = '';

    /**
     * 构建搜索 client
     * @param $key
     * @param $p
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    public function buildSearchClient($key, $p)
    {
        $client = new Client();
        $conf   = $this->buildSearchConf($key, $p);

        if (!isset($conf['options']['headers'])) {
            $conf['options']['headers'] = self::PC_HEADERS;
        }
        if (!isset($conf['options']['cookies'])) {
            $cookieJar = $this->getCookieJar($this->searchCookieKey);
            if ($cookieJar) {
                $conf['options']['cookies'] = $cookieJar;
            }
        }

        return $client->getAsync($conf['base_uri'], $conf['options']);
    }

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
    abstract protected function buildSearchConf($key, $p);

    /**
     * 过滤匹配搜索结果
     * @param $html
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-22
     */
    final public function searchTransform($html)
    {
        $this->saveText(static::class.'search_list', $html);
        try {
            $list = $this->_searchTransform($html);
            if (is_array($list)) {

            } elseif ($list instanceof ArrayAccess) {
                $list = $list->toArray();
            } else {
                throw new InvalidArgumentException('返回参数错误');
            }
            $list = array_values(array_filter($list));

            return $list;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    abstract protected function _searchTransform($html);

    /**
     * 获取目录
     * @param $bookId
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    abstract public function getCat($bookId);

    /**
     * 获取正文
     * @param $articleId
     * @param $bookId
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    abstract public function getArticle($articleId, $bookId);

    /**
     * 保存 cookie 字符串
     * @param $cookieStr
     * @param $key
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-22
     */
    public function saveCookie($cookieStr, $key = '')
    {
        $key = static::class.'-'.$key;
        $key = $this->formatKey($key);
        if (is_array($cookieStr)) {
            $cookieStr = $cookieStr[0];
        }
        file_put_contents(ROOT_PATH.'data'.DS.$key, $cookieStr);
    }

    /**
     * 获取 cookieJar 对象
     * @param $key
     * @return CookieJar|null
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-22
     */
    protected function getCookieJar($key)
    {
        $str = $this->getCookie($key);
        if ($str) {
            $cookieJar = new CookieJar();
            $cookieJar->setCookie(SetCookie::fromString($str));

            return $cookieJar;
        } else {
            return null;
        }
    }

    /**
     * 获取 cookie 字符串
     * @param $key
     * @return false|string|null
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-22
     */
    protected function getCookie($key = '')
    {
        $key = static::class.'-'.$key;
        $key = $this->formatKey($key);
        if (file_exists(ROOT_PATH.'data'.DS.$key)) {
            return file_get_contents(ROOT_PATH.'data'.DS.$key);
        } else {
            return null;
        }
    }

    protected function saveText($key, $content)
    {
        $key = $this->formatKey($key);
        file_put_contents(ROOT_PATH.'data'.DS.$key, $content);
    }

    protected function formatKey($key)
    {
        return str_replace(['\\', '/'], '_', $key);
    }

    /**
     * 获取 class 名字
     * @return bool|string
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-22
     */
    protected function getClassName()
    {
        if (empty($this->className)) {
            $this->className = substr(strrchr(static::class, '\\'), 1);
        }

        return $this->className;
    }

    abstract protected function getCatUrlId($url);

    abstract protected function decodeCatUrl($id);

    abstract protected function encodeArticleUrl($url);

    abstract protected function decodeArticleUrl($articleId, $bookId = null);

    /**
     * @param $url
     * @param array $options
     * @return string
     * @throws \Exception
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-23
     */
    protected function getHtml($url, $options = [])
    {
        $client = new Client();
        try {
            if (!isset($options['headers'])) {
                $options['headers'] = self::PC_HEADERS;
            }
            $response = $client->get($url, $options);
            if (!$response || $response->getStatusCode() !== 200) {
                throw new Exception($response->getReasonPhrase());
            }

            return (string) $response->getBody();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 过滤字符串
     * @param $html
     * @return string
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-23
     */
    protected function trim_html($html)
    {
        return trim(strip_tags(str_replace(["\r\n", "&nbsp;"], '', $html)));
    }

    /**
     * 转换字符集
     * @param $str
     * @param string $charset
     * @return string
     */
    protected function toUtf8($str, $charset = "GBK")
    {
        $r = iconv('GBK', 'UTF-8', $str);
        if (false === $r) {
            return '';
        } else {
            return $r;
        }
    }

}