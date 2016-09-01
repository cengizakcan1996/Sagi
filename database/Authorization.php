<?php
/**
 * Created by PhpStorm.
 * User: sagi
 * Date: 30.08.2016
 * Time: 17:12
 */

namespace Sagi\Database;

use Models\Auth;

/**
 * Class Authorization
 * @package Sagi\Database
 */
trait Authorization
{

    /**
     * @return mixed
     */
    public function getAuth(){
        return $this->hasOne(Auth::className(), ['user_id', 'id']);
    }
    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->is('superadmin');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is('admin');
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return $this->is('user');
    }

    /**
     * @return bool
     */
    public function isEditor()
    {
        return $this->is('editor');
    }

    /**
     * @param string $role
     * @return bool
     */
    public function is($role)
    {

        return RoleBag::hasPermission($this->getAuth()->role, $role);
    }

    /**
     * @param $id
     * @param $role
     */
    public function createUserAuth($id, $role)
    {
        $auth = new Auth();

        $auth->role = $role;
        $auth->user_id = $id;


        $auth->save();
    }


}