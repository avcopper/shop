<?php

namespace Models;

use System\Db;
use System\RSA;
use System\Request;
use Exceptions\DbException;
use Exceptions\UserException;

class UserProfile extends Model
{
    protected static $table = 'user_profiles';
    public $id;              // id
    public $active;              // активность
    public $user_id;         // id пользователя
    public $user_hash;    // хэш пользователя
    public $user_type_id;    // id типа пользователя (физическое/юридическое лицо)
    public $city_id;             // id города
    public $village_id;          // id деревни
    public $street_id;           // id улицы
    public $block_id;            // id блока
    public $structure_id;        // id планировочной структуры
    public $territory_id;        // id территории
    public $territory_street_id; // id улицы на территории
    public $house;        // дом
    public $building;            // корпус
    public $flat;                // квартира
    public $phone;        // телефон
    public $email;        // email
    public $name;         // контактное лицо
    public $comment;             // комментарий
    public $company;             // название организации
    public $address_legal;       // юридический адрес
    public $inn;                 // ИНН
    public $kpp;                 // КПП
    public $created;      // дата создания
    public $updated;             // дата обновления

    /**
     * Получает список профилей пользователя
     * @param int $user_id
     * @param bool $active
     * @param bool $object
     * @return array|false
     */
    public static function getListByUser(int $user_id, bool $active = true, $object = true)
    {
        return ($user_id !== '2') ?
            self::getListByUserId($user_id, $active, $object) :
            self::getListByUserHash($_COOKIE['user'], $active, $object);
    }

    /**
     * Получает список профилей по id пользователя
     * @param int $user_id
     * @param bool $active
     * @param bool $object
     * @return array|false
     * @throws DbException
     */
    public static function getListByUserId(int $user_id, bool $active = true, $object = true)
    {
        $activity = !empty($active) ? 'AND up.active IS NOT NULL AND u.active IS NOT NULL AND u.blocked IS NULL' : '';
        $params = [
            ':user_id' => $user_id
        ];
        $sql = "
            SELECT up.id, up.user_id, up.user_hash, up.user_type_id, 
                   up.city_id, c.name city, sn1.shortname city_shortname, 
                   up.street_id, s.name street, sn2.shortname street_shortname, 
                   up.house, up.building, up.flat, 
                   up.phone, up.email, up.name, up.comment, 
                   up.company, up.address_legal, up.inn, up.kpp, 
                   up.created, up.updated,                   
                   u.last_name AS user_last_name, u.name AS user_name, u.second_name AS user_second_name, 
                   u.email AS user_email, u.phone AS user_phone 
            FROM user_profiles up 
            LEFT JOIN users u 
                ON up.user_id = u.id 
            LEFT JOIN fias_cities c 
                ON c.id = up.city_id 
            LEFT JOIN fias_streets s 
                ON s.id = up.street_id 
            LEFT JOIN fias_shortnames sn1 
                ON sn1.id = c.shortname_id 
            LEFT JOIN fias_shortnames sn2 
                ON sn2.id = s.shortname_id 
            WHERE up.user_id = :user_id {$activity}
            ";
        $db = new Db();
        $data = $db->query($sql, $params, $object ? static::class : null);
        return $data ?? false;
    }

    /**
     * Получает список профилей по hash пользователя
     * @param $user_hash
     * @param bool $active
     * @param bool $object
     * @return array|false
     * @throws DbException
     */
    public static function getListByUserHash($user_hash, bool $active = true, $object = true)
    {
        $activity = !empty($active) ? ' AND up.active IS NOT NULL AND u.active IS NOT NULL AND u.blocked IS NULL' : '';
        $params = [
            ':user_hash' => $user_hash
        ];
        $sql = "
            SELECT up.id, up.user_id, up.user_hash, up.user_type_id, 
                   up.city_id, c.name city, sn1.shortname city_shortname, 
                   up.street_id, s.name street, sn2.shortname street_shortname, 
                   up.house, up.building, up.flat, 
                   up.phone, up.email, up.name, up.comment, 
                   up.company, up.address_legal, up.inn, up.kpp, 
                   up.created, up.updated,                   
                   u.last_name AS user_last_name, u.name AS user_name, u.second_name AS user_second_name, 
                   u.email AS user_email, u.phone AS user_phone 
            FROM user_profiles up 
            LEFT JOIN users u 
                ON up.user_id = u.id 
            LEFT JOIN fias_cities c 
                ON c.id = up.city_id 
            LEFT JOIN fias_streets s 
                ON s.id = up.street_id 
            LEFT JOIN fias_shortnames sn1 
                ON sn1.id = c.shortname_id 
            LEFT JOIN fias_shortnames sn2 
                ON sn2.id = s.shortname_id
            WHERE u.id = 2 AND up.user_hash = :user_hash {$activity}
            ";
        $db = new Db();
        $data = $db->query($sql, $params, $object ? static::class : null);
        return $data ?? false;
    }

    /**
     * Сохранение профиля пользователя
     * @param array $form
     * @param User $user
     * @param int $user_type
     * @param bool $isAjax
     * @return bool|int
     * @throws DbException
     * @throws UserException
     */
    public function saveProfile(array $form, User $user, int $user_type, bool $isAjax)
    {
        if ($user_type === 1) return $this->savePhisicalProfile($form, $user, $isAjax);
        elseif ($user_type === 2) return $this->saveJuridicalProfile($form, $user, $isAjax);
        return false;
    }

    /**
     * Сохранение профиля физического лица
     * @param array $form
     * @param User $user
     * @param bool $isAjax
     * @return bool|int
     * @throws DbException
     * @throws UserException
     */
    protected function savePhisicalProfile(array $form, User $user, bool $isAjax)
    {
        $user_profile =
            (Request::post('p_profile') === '0') ?
                (new UserProfile()) :
                self::getByIdAndUserId(Request::post('p_profile'), $user->id);

        $user_profile->active       = 1;
        $user_profile->user_id      = $user->id;
        $user_profile->user_hash    = $_COOKIE['user'];
        $user_profile->user_type_id = 1;
        $user_profile->phone        = trim($form['p_phone']);
        $user_profile->email        = trim($form['p_email']);
        $user_profile->name         = (new RSA($user->private_key))->encrypt(trim($form['p_name']));
        $user_profile->created      = $user_profile->created ?? date('Y-m-d');
        $user_profile->updated      = $user_profile->id ? date('Y-m-d') : null;

        if (intval($form['delivery']) !== 1) {
            $user_profile->city_id   = intval($form['city_id']);
            $user_profile->street_id = intval($form['street_id']);
            $user_profile->house     = trim($form['house']);
            $user_profile->building  = trim($form['building']) ?: null;
            $user_profile->flat      = trim($form['flat']) ?: null;
            $user_profile->comment   = trim($form['comment']) ?: null;
        }

        $profile_id = $user_profile->save();

        if (!$profile_id) self::returnError('Не удалось сохранить профиль пользователя', $isAjax);
        return $profile_id;
    }

    protected function saveJuridicalProfile($form, $user, $isAjax)
    {
        $user_profile =
            (Request::post('j_profile') === '0') ?
                (new UserProfile()) :
                self::getByIdAndUserId(Request::post('j_profile'), $user->id);

        $user_profile->active = 1;
        $user_profile->user_id = $user->id;
        $user_profile->user_hash = $_COOKIE['user'];
        $user_profile->user_type_id = 2;
        $user_profile->phone = trim($form['j_phone']);
        $user_profile->email = trim($form['j_email']);
        $user_profile->name = (new RSA($user->private_key))->encrypt(trim($form['j_name']));
        $user_profile->company = trim($form['company']);
        $user_profile->address_legal = trim($form['j_address']);
        $user_profile->inn = trim($form['inn']);
        $user_profile->kpp = trim($form['kpp']);
        $user_profile->created = $user_profile->created ?? date('Y-m-d');
        $user_profile->updated = $user_profile->id ? date('Y-m-d') : null;

        if (intval($form['delivery']) !== 1) {
            $user_profile->city_id   = intval($form['city_id']);
            $user_profile->street_id = intval($form['street_id']);
            $user_profile->house     = trim($form['house']);
            $user_profile->building  = trim($form['building']) ?: null;
            $user_profile->flat      = trim($form['flat']) ?: null;
            $user_profile->comment   = trim($form['comment']) ?: null;
        }

        $profile_id = $user_profile->save();

        if (!$profile_id) self::returnError('Не удалось сохранить профиль пользователя', $isAjax);
        return $profile_id;
    }





























    public static function getByIdAndUserId(int $id, int $user_id, bool $active = true, bool $object = true)
    {
        $where = !empty($active) ? ' AND active IS NOT NULL' : '';
        $sql = "SELECT * FROM " . static::$table . " WHERE id = :id AND user_id = :user_id {$where}";
        $params = [
            ':id' => $id,
            ':user_id' => $user_id
        ];
        $db = new Db();
        $data = $db->query($sql, $params, $object ? static::class : null);
        return !empty($data) ? array_shift($data) : false;
    }
}
