<?php

class App_Service_Search
{

    private $_db;

    private $_clientColumns = array(
        'c.client_id',
        'c.first_name',
        'c.last_name',
        'c.cell_phone',
        'c.home_phone',
        'c.work_phone',
    );

    private $_householdColumns = array(
        'h.address_id',
        'h.mainclient_id',
        'h.current_flag',
    );

    private $_addrColumns = array(
        'a.address_id',
        'a.street',
        'a.apt',
        'a.city',
        'a.state',
        'a.zipcode',
    );

    private $_clientOrderColumns = array('last_name', 'first_name', 'client_id');

    public function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }


    public function getClientsByName($name)
    {
        $likeName = '%' . App_Escaping::escapeLike($name) . '%';
        $select   = $this->initClientSelect()
            ->where('c.last_name LIKE ? OR c.first_name LIKE ?', $likeName, $likeName);
        $results  = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    public function getClientsByAddr($addr)
    {
        $likeAddr = '%' . App_Escaping::escapeLike($addr) . '%';
        $select  = $this->initClientSelect()
            ->where(
                'a.street LIKE ? OR a.apt LIKE ? OR a.city LIKE ? OR a.state LIKE ?'
                    . ' OR a.zipcode LIKE ?',
                $likeAddr,
                $likeAddr,
                $likeAddr,
                $likeAddr,
                $likeAddr
            );
        $results = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    public function getClientsByPhone($phone)
    {
        $select  = $this->initClientSelect()
            ->where(
                'c.cell_phone = ? OR c.home_phone = ? OR c.work_phone = ?',
                $phone,
                $phone,
                $phone
            );
        $results = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    private function initClientSelect()
    {
        return $this->_db->select()
            ->from(array('c' => 'client'), $this->_clientColumns)
            ->join(
                array('h' => 'household'),
                'c.client_id = h.mainclient_id',
                $this->_householdColumns
            )
            ->join(
                array('a' => 'address'),
                'a.address_id = h.address_id',
                $this->_addrColumns)
            ->where('h.current_flag = 1')
            ->order($this->_clientOrderColumns);
    }

    private function buildClientModels($dbResults)
    {
        $clients = array();

        foreach ($dbResults as $dbResult) {
            $addr = new Application_Model_Addr();
            $addr
                ->setId($dbResult['address_id'])
                ->setStreet($dbResult['street'])
                ->setApt($dbResult['apt'])
                ->setCity($dbResult['city'])
                ->setState($dbResult['state'])
                ->setZip($dbResult['zipcode']);

            $client = new Application_Model_Client();
            $client
                ->setId($dbResult['client_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setCellPhone($dbResult['cell_phone'])
                ->setHomePhone($dbResult['home_phone'])
                ->setWorkPhone($dbResult['work_phone'])
                ->setCurrentAddr($addr);

            $clients[] = $client;
        }

        return $clients;
    }
}
