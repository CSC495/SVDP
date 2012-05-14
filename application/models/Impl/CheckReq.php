<?php

/**
 * Model class representing a check request associated with some case need.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$case->setId(...)->setRequestDate(...)` and so on.
 */
class Application_Model_Impl_CheckReq
{

    private $_id = null;
    private $_caseneedId = null;
    private $_requestDate = null;
    //Case id, not object
    private $_case = null;
    //User object or id depending on use
    private $_user = null;
    private $_amount = null;
    private $_comment = null;
    //User object or id depending on use
    private $_signeeUser = null;
    private $_checkNumber = null;
    private $_issueDate = null;
    private $_accountNumber = null;
    private $_payeeName = null;
    //Addr object
    private $_addr = null;
    private $_phone = null;
    private $_contactFirstName = null;
    private $_contactLastName = null;

    /* Generic get/set methods: */

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    public function getCaseNeedId(){
        return $this->_caseneedId;
    }
    
    public function setCaseNeedId($id){
        $this->_caseneedId = $id;
        return $this;
    }
    
    public function getUser(){
        return $this->_user;
    }
    
    public function setUser($user){
        $this->_user = $user;
        return $this;
    }

    public function getRequestDate()
    {
        return $this->_requestDate;
    }

    public function setRequestDate($requestDate)
    {
        $this->_requestDate = $requestDate;
        return $this;
    }

    public function getCase()
    {
        return $this->_case;
    }

    public function setCase($case)
    {
        $this->_case = $case;
        return $this;
    }
    
    
    public function getAmount(){
        return $this->_amount;
    }
    
    public function setAmount($amount){
        $this->_amount = $amount;
        return $this;
    }
    
    public function getComment(){
         return $this->_comment;
    }
    
    public function setComment($comment){
        $this->_comment = $comment;
        return $this;
    }
    public function getSigneeUser(){
         return $this->_signeeUser;
    }
    
    public function setSigneeUser($user){
        $this->_signeeUser = $user;
        return $this;
    }
    public function getCheckNumber(){
         return $this->_checkNumber;
    }
    
    public function setCheckNumber($checkNumber){
        $this->_checkNumber = $checkNumber;
        return $this;
    }
    
    public function getIssueDate(){
         return $this->_issueDate;
    }
    
    public function setIssueDate($issue){
        $this->_issueDate = $issue;
        return $this;
    }
    
    public function getAccountNumber(){
        return $this->_accountNumber;
    }
    
    public function setAccountNumber($accNum){
        $this->_accountNumber = $accNum;
        return $this;
    }
    
    public function getPayeeName(){
         return $this->_payeeName;
    }
    
    public function setPayeeName($name){
        $this->_payeeName = $name;
        return $this;
    }
    public function getAddress(){
         return $this->_addr;
    }
    
    public function setAddress($addr){
        $this->_addr = $addr;
        return $this;
    }
    public function getPhone(){
         return $this->_phone;
    }
    
    public function setPhone($phone){
        $this->_phone = $phone;
        return $this;
    }
    public function getContactFirstName(){
         return $this->_contactFirstName;
    }
    
    public function setContactFirstName($fName){
        $this->_contactFirstName = $fName;
        return $this;
    }
    public function getContactLastName(){
         return $this->_contactLastName;
    }
    
    public function setContactLastName($lName){
        $this->_contactLastName = $lName;
        return $this;
    }
}
