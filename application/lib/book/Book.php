<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 17:03
 */

namespace app\lib\book;

use function GuzzleHttp\Promise\unwrap;
use function in_array;

class Book
{

    const SITES = ['Qisuu'];

    const SITE_NAMESPACE_PREFIX = 'app\\lib\\book\\siteImpl\\';

    /**
     * 搜索列表
     * @param $key
     * @param $p
     * @return array
     * @throws \Throwable
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-23
     */
    public static function search($key, $p)
    {
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
        $list    = [];
        try {
            $results = unwrap($siteClients);
            foreach ($results as $site => $response) {
                if ($response && 200 == $response->getStatusCode()) {
                    $siteInstances[ $site ]->saveCookie($response->getHeader('set-cookie'),
                                                        $siteInstances[ $site ]->searchCookieKey);
                    $arr  = $siteInstances[ $site ]->searchTransform((string) $response->getBody());
                    $list += $arr;
                }
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $list;
    }

    public static function cat($type, $id)
    {
        if (!in_array($type, self::SITES)) {
            throw new \InvalidArgumentException('参数错误');
        }
        $cls = self::SITE_NAMESPACE_PREFIX.$type;
        /**
         * @var $instance AbstractSite
         */
        $instance = new $cls();

        return $instance->getCat($id);

    }

}