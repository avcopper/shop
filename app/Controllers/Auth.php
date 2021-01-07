<?php

namespace App\Controllers;

use App\Models\User;
use App\System\Request;
use \App\Exceptions\DbException;
use App\Exceptions\UserException;

class Auth extends Controller
{
    protected function before()
    {
        if (User::isAuthorized() &&
            (empty(ROUTE[1]) || !in_array(ROUTE[1], ['Logout']))
        ) {
            header('Location: /personal/');
            die;
        }
    }

    /**
     * Авторизация
     * @throws DbException
     * @throws UserException
     */
    protected function actionDefault()
    {
        if (Request::isPost()) {
            if (User::authorization(Request::post(), Request::isAjax())) {
                header('Location: /');
                die;
            }
        } else {
            $this->view->display('auth/auth');
            die;
        }
    }

    /**
     * Регистрация
     * @throws DbException
     * @throws UserException
     */
    protected function actionRegistration()
    {
        if (!empty($this->view->user['id'])) {
            header('Location: /personal/');
            die;
        }

        if (Request::isPost()) $this->view->register = User::register(Request::post(), Request::isAjax());

        $this->view->display('auth/register');
        die;
    }

    /**
     * Восстановление пароля
     * @throws UserException
     */
    protected function actionRestore()
    {
        if (Request::isPost()) {
            $this->view->restore = User::restore(Request::post(), Request::isAjax());
        }

        $this->view->display('auth/restore');
        die;
    }

    /**
     * Подтверждение регистрации
     */
    protected function actionConfirm()
    {
        $hash = Request::get('hash');
        if (!empty($hash)) $this->view->success = User::confirm($hash);

        $this->view->display('auth/confirm');
        die;
    }

    /**
     * Выход
     * @throws DbException
     */
    protected function actionLogout()
    {
        User::logout();
        header('Location: /');
        die;
    }
}
