<?php
//Service File for Admin Controller
//Authored by: Matthew Tieman
class App_Service_MemberService {
    protected $db;
    function __construct(){
$this->db = Zend_Db_Table::getDefaultAdapter();
    }
    
}