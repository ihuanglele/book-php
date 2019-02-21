<?php
/**
 * Created by PhpStorm.
 * Author: ihuanglele<huanglele@yousuowei.cn>
 * Date: 2019-01-21
 * Time: 17:03
 */

namespace app\lib\book;

use fw\Container;
use function GuzzleHttp\Promise\unwrap;
use function in_array;
use function time;

class Book
{

    const SITES = ['Qisuu', 'Yznnw'];
//    const SITES = ['Yznnw'];

    const SITE_NAMESPACE_PREFIX = 'app\\lib\\book\\siteImpl\\';

    /**
     * 搜索列表
     * @param $key
     * @param $p
     * @return array
     * @throws \Throwable
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-23
     */
    public static function search($key, $p)
    {
        $ck  = $key.$p;
        $res = Container::getCache()->get($ck);
        if ($res) {
            return $res;
        }
        $siteInstances = [];
        $siteClients   = [];
        foreach (self::SITES as $site) {
            $cls = self::SITE_NAMESPACE_PREFIX.$site;
            /**
             * @var $obj AbstractSite
             */
            $obj                    = new $cls;
            $siteClients[ $site ]   = $obj->buildSearchClient($key, $p);
            $siteInstances[ $site ] = $obj;
        }
        $list = [];
        try {
            $results = unwrap($siteClients);
            foreach ($results as $site => $response) {
                if ($response && 200 == $response->getStatusCode()) {
                    $siteInstances[ $site ]->saveCookie($response->getHeader('set-cookie'),
                                                        $siteInstances[ $site ]->searchCookieKey);
                    $arr  = $siteInstances[ $site ]->searchTransform((string) $response->getBody());
                    $list = array_merge($list, $arr);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
        Container::getCache()->set($ck, $list, time() + 36000);

        return $list;
    }

    /**
     * 获取书
     * @param $type
     * @param $bookId
     * @return mixed
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-25
     */
    public static function cat($type, $bookId)
    {
        if (!in_array($type, self::SITES) || !$bookId) {
            throw new \InvalidArgumentException('参数错误');
        }
        $key     = $type.$bookId;
        $content = Container::getCache()->get($key);
        if ($content) {
            return $content;
        } else {
            $cls = self::SITE_NAMESPACE_PREFIX.$type;
            /**
             * @var $instance AbstractSite
             */
            $instance = new $cls();

            $content = $instance->getCat($bookId);
            if ($content) {
                Container::getCache()->set($key, $content, 0);
            }

            return $content;
        }
    }

    /**
     * 获取文章内容
     * @param $type
     * @param $bookId
     * @param $articleId
     * @return string
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-25
     */
    public static function article($type, $bookId, $articleId)
    {
        if (!in_array($type, self::SITES)) {
            throw new \InvalidArgumentException('参数错误');
        }
        $key     = $type.$bookId.$articleId;
        $content = Container::getCache()->get($key);
        if ($content) {
            return $content;
        } else {
            $cls = self::SITE_NAMESPACE_PREFIX.$type;
            /**
             * @var $instance AbstractSite
             */
            $instance = new $cls();

            $content = $instance->getArticle($articleId, $bookId);
            if ($content) {
                Container::getCache()->set($key, $content, 0);
            }

            return $content;
        }
    }

}