<?php
//Class represents all information associated with a single case visit.
//Typically used in array of CaseVisit objects to represent visit history of a paticular case
//Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
//be chained together: `$case->setId(...)->setOpenedDate(...)` and so on.
class Application_Model_Impl_CaseVisit{
    private $_visitId = null;
    private $_visitDate = null;
    private $_miles = null;
    private $_hours = null;
    //Array of User objects
    private $_visitors = null;
    
    //Generic getter and setter methods
    public function getVisitId(){
        return $this->_visitId;
    }
    
    public function setVisitId($id){
        $this->_visitId = $id;
        return $this;
    }
    
    public function getVisitDate(){
        return $this->_visitDate;
    }
    
    public function setVisitDate($date){
        $this->_visitDate = $date;
        return $this;
    }
    
    public function getMiles(){
        return $this->_miles;
    }
    
    public function setMiles($miles){
        $this->_miles = $miles;
        return $this;
    }
    
    public function getHours(){
        return $this->_hours;
    }
    
    public function setHours($hours){
        $this->_hours = $hours;
        return $this;
    }
    
    public function getVisitors(){
        return $this->_visitors;
    }
    
    public function setVisitors($visitors){
        $this->_visitors = $visitors;
        return $this;
    }
}