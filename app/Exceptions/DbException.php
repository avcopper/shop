<?php

namespace Exceptions;

/**
 * Class DbException
 * @package App\Exceptions
 */
class DbException extends BaseException
{
    protected $code = 500;
    protected $error = 'Внутренняя ошибка сервера';
    protected $message = 'Что-то пошло не так. Зайдите позже';
}
