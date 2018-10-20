<?php
class Login_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }
    public function getLogin($login){
        return $this->db->get_where('user', array('email' => $login));
    }
}