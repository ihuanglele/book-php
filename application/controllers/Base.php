<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-21
 * Time: 16:03
 */


class BaseController extends \fw\BaseController
{

    /**
     * getParam
     * @param $name
     * @param null $default
     * @return mixed
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    protected function getParam($name, $default = null)
    {
        return $this->getRequest()->getQuery($name, $default);
    }

    /**
     * getParams
     * @return array
     * @author 晃晃<wangchunhui@doweidu.com>
     * @time 2019-01-21
     */
    protected function getParams()
    {
        return $this->getRequest()->getParams();
    }

}