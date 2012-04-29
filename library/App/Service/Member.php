<?php

class App_Service_Member
{

    private $_db;

    public function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    public function getClientById($clientId)
    {
        $select = $this->_db->select()
            ->from(array('c' => 'client'), array(
                'c.client_id',
                'c.created_user_id',
                'c.first_name',
                'c.last_name',
                'c.other_name',
                'c.marriage_status',
                'birthdate' => 'DATE_FORMAT(c.birthdate, "%m/%d/%Y")',
                'c.ssn4',
                'c.cell_phone',
                'c.home_phone',
                'c.work_phone',
                'c.created_date',
                'c.member_parish',
                'c.veteran_flag',
            ))
            ->join(
                array('h' => 'household'),
                'c.client_id = h.mainclient_id OR c.client_id = h.spouse_id',
                array()
            )
            ->join(
                array('a' => 'address'),
                'a.address_id = h.address_id',
                array(
                    'a.address_id',
                    'a.street',
                    'a.apt',
                    'a.city',
                    'a.state',
                    'a.zipcode',
                    'a.reside_parish',
                )
            )
            ->joinLeft(
                array('d' => 'do_not_help'),
                'c.client_id = d.client_id',
                array('do_not_help_client_id' => 'd.client_id')
            )
            ->where('h.current_flag = 1')
            ->where('c.client_id = ?', $clientId);

        $results = $this->_db->fetchRow($select);
        return $this->buildClientModel($results);
    }

    private function buildClientModel($dbResult)
    {
        $addr = new Application_Model_Impl_Addr();
        $addr
            ->setId($dbResult['address_id'])
            ->setStreet($dbResult['street'])
            ->setApt($dbResult['apt'])
            ->setCity($dbResult['city'])
            ->setState($dbResult['state'])
            ->setZip($dbResult['zipcode'])
            ->setParish($dbResult['reside_parish']);

        $client = new Application_Model_Impl_Client();
        $client
            ->setId($dbResult['client_id'])
            ->setUserId($dbResult['created_user_id'])
            ->setFirstName($dbResult['first_name'])
            ->setLastName($dbResult['last_name'])
            ->setOtherName($dbResult['other_name'])
            ->setMarried($dbResult['marriage_status'])
            ->setBirthDate($dbResult['birthdate'])
            ->setSsn4($dbResult['ssn4'])
            ->setCellPhone($dbResult['cell_phone'])
            ->setHomePhone($dbResult['home_phone'])
            ->setWorkPhone($dbResult['work_phone'])
            ->setCreatedDate($dbResult['created_date'])
            ->setParish($dbResult['member_parish'])
            ->setVeteran($dbResult['veteran_flag'])
            ->setCurrentAddr($addr)
            ->setDoNotHelp($dbResult['do_not_help_client_id'] !== null);

        return $client;
    }
}
