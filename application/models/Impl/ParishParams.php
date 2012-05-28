<?php
/**
 * Class implements model which holds all the parish specific parameters
 */
class Application_Model_Impl_ParishParams
{
    /**
     * The current funds available for the parish
     * @var float
     */
    private $_fundsAvailable;
    /**
     * Number of cases a client may have in a year
     * @var int
     */
    private $_yearLimit;
    /**
     * Dollar amount of aid a client may receive in their lifetime
     * @var float
     */
    private $_lifeTimeLimit;
    /**
     * Number of cases a client may have in their lifetime
     * @var int
     */
    private $_caseLimit;
    /**
     * Dollar amount limit a particular case may have
     * @var float
     */
    private $_caseFundLimit;
    
    /**
     * Constructor for building a paraish params object
     *
     * @param float $funds Total funds available for the parish
     * @param int $year Number of cases a client may have in a year
     * @param float $lifetime Dollar amount of aid a client may receive in their lifetime
     * @param int $caseLimit Number of cases a client may have in their lifetime
     * @param float $caseFundLimit Dollar amount limit a particular case may have
     */
    public function __construct($funds,$year,$lifetime,$caseLimit,$caseFundLimit)
    {
        $this->_fundsAvailable = $funds;
        $this->_yearLimit = $year;
        $this->_lifeTimeLimit = $lifetime;
        $this->_caseLimit = $caseLimit;
        $this->_caseFundLimit = $caseFundLimit;
    }

    /**
     * Gets the amount of funds currently available
     *
     * @return float Total amount of funds available
     */
    public function getAvailableFunds(){
        return $this->_fundsAvailable;
    }
    /**
     * Sets the amount of funds available
     *
     * @param float $amt Amount of funds currently available
     * @return Application_Model_Impl_ParishParams this
     */
    public function setAvailableFunds($amt){
        $this->_fundsAvailable = $amt;
        return $this;
    }
    /**
     * Gets the number of cases a client may have in a year
     *
     * @retun int Number of cases a client may have per year
     */
    public function getYearlyLimit(){
        return $this->_yearLimit;
    }
    /**
     * Sets the number of cases a client may have per year
     *
     * @param int $amt Number of cases a client may have per year
     * @return Application_Model_Impl_ParishParams this
     */
    public function setYearlyLimit($amt){
        $this->_yearLimit = $amt;
        return $this;
    }
    /**
     * Sets the dollar amount of aid a client may receive in their lifetime
     *
     * @param float $amt Amount of funds a client may receive in their lifetime
     * @return Application_Model_Impl_ParishParams this
     */
    public function setLifeTimeLimit($amt){
        $this->_lifeTimeLimit = $amt;
        return $this;
    }
    /**
     * Gets the dollar amount of aid a client may receive in their lifetime
     *
     * @return float Dollar amount of aid a client may receive in their lifetime
     */
    public function getLifeTimeLimit(){
        return $this->_lifeTimeLimit;
    }
    /**
    * Sets the number of cases a client may have in their lifetime
    *
    * @param int $amt Number of cases a client may have in their lifetime
    * @return Application_Model_Impl_ParishParams this
    */
    public function setCaseLimit($amt){
        $this->_caseLimit = $amt;
        return $this;
    }
    /**
     * Gets the number of cases a client may have in their lifetime
     *
     * @return int number of cases a client may have in their lifetime
     */
    public function getCaseLimit(){
        return $this->_caseLimit;
    }
    /**
    * Sets the dollar amount limit per case
    *
    * @param int $amt The dollar amount limit per case
    * @return Application_Model_Impl_ParishParams this
    */
    public function setCaseFundLimit($amt){
        $this->_caseFundLimit = $amt;
        return $this;
    }
    /**
    * Gets the dollar amount limit per case
    *
    * @return int The dollar amount limit per case
    */
    public function getCaseFundLimit(){
        return $this->_caseFundLimit;
    }
}
