<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 17:03
 */

namespace app\lib\book;


use app\lib\book\Siteimpl\Qisuu;
use function GuzzleHttp\Promise\unwrap;
use function var_dump;

class Book
{

    public static function search($key, $p)
    {
        $qisuu   = new Qisuu();
        $qC      = $qisuu->search($key, $p);
        $results = unwrap(['qisuu' => $qC]);
        var_dump($results['qisuu']->getHeaders());
    }

}