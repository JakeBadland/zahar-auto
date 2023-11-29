<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Libraries\LibBcrypt;

class UserModel extends Model{

    public $user;

    protected string $table   = 'users';

    /*
    public function __construct(?ConnectionInterface $db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }
    */

    public function auth($data) : bool
    {
        $bcrypt = new LibBcrypt();

        $user = $this->db->table('users')->select('*')->getWhere(['login' => $data['login']])->getRow();

        if (!$user) return false;

        $result = $bcrypt->check_password($data['password'], $user->password);

        if ($result && !$this->user){
            $this->user = $user;
            $this->saveSession();
        }

        return (bool) $result;
    }

    public function logout()
    {
        $session = \Config\Services::session();

        $session->set('uToken', null);

        if ($this->user){
            $user = $this->get();
            $this->db->table('users')->where(['id' => $user->id])->set(['token' => null])->update();
        }
    }

    public function get()
    {
        $session = \Config\Services::session();

        if (!$this->user){
            $token = $session->get('uToken');

            if (!$token) return null;

            $this->user = $this->db->table('users')->select('*')->getWhere(['token' => $token])->getRow(0);

            if (!$this->user){
                return null;
            }
        }

        return $this->user;
    }

    private function saveSession()
    {
        $session = \Config\Services::session();

        $userToken = uniqid('', true);

        $data = ['token' => $userToken];

        $this->db->table('users')->where(['id' => $this->user->id])->set($data)->update();
        $session->set('uToken', $userToken);
    }

    public function addUser($data)
    {
        $bcrypt = new LibBcrypt();

        $data['role_id'] = $this->getRoleId($data['role']);
        unset($data['confirm']);
        unset($data['role']);

        $data['password'] = $bcrypt->hash_password($data['password']);
        $this->db->table('users')->insert($data);
    }

    public function updateUser($data){
        $userId = $data['id'];
        unset($data['id']);
        unset($data['confirm']);

        $data['role_id'] = $this->getRoleId($data['role']);
        unset($data['role']);

        $bcrypt = new LibBcrypt();

        $data['password'] = $bcrypt->hash_password($data['password']);

        $this->db->table('users')->set($data)->where('id', $userId)->update();
    }

    public function getRoleId($roleName)
    {
        return $this->db->table('roles')->select('*')->getWhere(['name' => $roleName])->getRow(0)->id;
    }

    public function getRoleName($roleId)
    {
        return $this->db->table('roles')->select('*')->getWhere(['id' => $roleId])->getRow(0)->name;
    }

    public function getById($userId)
    {
        return $this->db->table('users')->select('*')->getWhere(['id' => $userId])->getRow(0);
    }

    public function updateGameInfo($userId, $data)
    {
        $this->db->table('user_info')->set($data)->where('user_id', $userId)->update();
    }
}