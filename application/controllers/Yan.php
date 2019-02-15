<?php

use app\models\User;
use Yaf\Session;

/**
 * Created by PhpStorm.
 * Author ihuanglele<huanglele@yousuowei.cn>
 * Date: 2019-01-31
 * Time: 16:13
 */
class YanController extends BaseController
{

    private $mustLoginAction = [
        'index',
        'add',
        'del',
    ];

    /**
     * @var Session
     */
    private $sessionIns = null;

    private $token = '';

    public function init()
    {
        parent::init();
        if (isset($_SERVER['HTTP_TOKEN']) && $_SERVER['HTTP_TOKEN']) {
            $this->token = $_SERVER['HTTP_TOKEN'];
        } else {
            $this->token = uniqid();
        }
        session_id($this->token);
        session_start([
                          'cookie_lifetime' => 86400,
                      ]);
        $this->sessionIns = Session::getInstance();
        $this->sessionIns->start();
        $this->checkLogin();
    }

    private function checkLogin()
    {
        if (in_array($this->getRequest()->getActionName(), $this->mustLoginAction)) {
            $aid = $this->sessionIns->get('aid');
            if (!$aid) {
                $this->error('请先登录', 400);
            }
        }
    }

    public function indexAction()
    {
        $where = [];
        $name  = $this->get('name');
        if ($name) {
            $where['name[~]'] = $name;
        }

        $tel = $this->get('tel');
        if ($tel) {
            $where['tel[~]'] = $tel;
        }

        $User           = new User();
        $where['ORDER'] = [
            'uid' => 'DESC',
        ];
        $where['LIMIT'] = 20;


        $this->success($this->get());
        $r = $User->select('*', $where);
        $this->success(['sql' => $User->log(), 'list' => $r]);
    }

    public function loginAction()
    {
        $name     = $this->post('name', '');
        $password = $this->post('password', '');
        if (empty($name) || empty($password)) {
            $this->error('参数错误');
        }
        if ($name === 'admin' && $password === '00002222') {
            $this->sessionIns->set('aid', $name);
            $this->success(['token' => $this->token]);
        } else {
            $this->error('密码错误');
        }
    }

    public function logoutAction()
    {
        $this->sessionIns->del('aid');
        $this->success('退出登录');
    }
}