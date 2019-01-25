<?php
/**
 * Created by PhpStorm.
 * Author: ihuanglele<huanglele@yousuowei.cn>
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
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    protected function getParam($name, $default = null)
    {
        return $this->getRequest()->getQuery($name, $default);
    }

    /**
     * getParams
     * @return array
     * @author ihuanglele<huanglele@yousuowei.cn>
     * @time 2019-01-21
     */
    protected function getParams()
    {
        return $this->getRequest()->getParams();
    }

}