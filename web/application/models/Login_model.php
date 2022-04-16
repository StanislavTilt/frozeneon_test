<?php

namespace Model;

use App;
use Exception;
use System\Core\CI_Model;

class Login_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();

    }

    public static function logout()
    {
        App::get_ci()->session->unset_userdata('id');
    }

    /**
     * @param array $loginData
     * @return User_model
     * @throws Exception
     */
    public static function login(array $loginData): User_model
    {
        $user = User_model::find_user_by_email($loginData['email']);

        if(!$user->validatePassword($loginData['password'])) throw new Exception(validation_errors());

        self::start_session($user->get_id());
        return $user;
    }

    public static function start_session(int $user_id)
    {
        // если перенедан пользователь
        if (empty($user_id))
        {
            throw new Exception('No id provided!');
        }

        App::get_ci()->session->set_userdata('id', $user_id);
    }
}
