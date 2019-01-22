<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 17:03
 */

namespace app\lib\book;

use function GuzzleHttp\Promise\unwrap;

class Book
{

    public static function search($key, $p)
    {
        $sites         = ['Qisuu'];
        $siteInstances = [];
        $siteClients   = [];
        foreach ($sites as $site) {
            $cls = 'app\\lib\\book\\siteImpl\\'.$site;
            /**
             * @var $obj AbstractSite
             */
            $obj                    = new $cls;
            $siteClients[ $site ]   = $obj->buildSearchClient($key, $p);
            $siteInstances[ $site ] = $obj;
        }
        $results = unwrap($siteClients);
        $list    = [];
        foreach ($results as $site => $response) {
            if ($response && 200 == $response->getStatusCode()) {
                $siteInstances[ $site ]->saveCookie($response->getHeader('set-cookie'),
                                                    $siteInstances[ $site ]->searchCookieKey);
                $arr  = $siteInstances[ $site ]->searchTransform((string) $response->getBody());
                $list += $arr;
            }
        }

        return $list;
    }

}