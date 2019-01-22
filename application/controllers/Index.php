<?php

use app\lib\book\Book;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 16:03
 */

class IndexController extends BaseController
{

    public function indexAction()
    {
        $this->success('ok');
    }

    public function searchAction()
    {
        $key = $this->getParam('key', '');
        $p   = $this->getParam('p', 1);
        $this->success(Book::search($key, $p));
    }

    public function testAction()
    {
        $query   = [
            'q'  => 'key',
            'p'  => 0,
            'cc' => 'qisuu.la',
        ];
        $jar     = new CookieJar();
        $default =
            'BAIDUID=F2BDC30EE76DF13862B4094C23A5B494:FG=1; expires=Wed, 22-Jan-20 01:23:04 GMT; max-age=31536000; path=/; domain=.baidu.com; version=1';
        $c       = file_get_contents('cookie');
        $jar->setCookie(GuzzleHttp\Cookie\SetCookie::fromString($c ?? $default));

        $client   = new Client();
        $response = $client->get('http://zhannei.baidu.com/cse/site/',
                                 [
                                     'query'   => $query,
                                     'headers' => [
                                         'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
                                         'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                                     ],
                                     'debug'   => true,
                                     'cookies' => $jar,
                                 ]);
        var_dump('----------');
        if ($response) {
            $cookie = $response->getHeader('set-cookie');
            if ($cookie) {
                file_put_contents('cookie', $cookie);
            }
            var_dump((string) $response->getBody());
        } else {
            echo 'request error';
        }

    }



}