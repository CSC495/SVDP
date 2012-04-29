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
            ->joinLeft(
                array('c2' => 'client'),
                'c2.client_id <> c.client_id '
                . 'AND (c2.client_id = h.mainclient_id OR c2.client_id = h.spouse_id)',
                array(
                    'spouse_id' => 'c2.client_id',
                    'spouse_first_name' => 'c2.first_name',
                    'spouse_birthdate' => 'DATE_FORMAT(c2.birthdate, "%m/%d/%Y")',
                )
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
                array('do_not_help_reason' => 'd.reason')
            )
            ->where('h.current_flag = 1')
            ->where('c.client_id = ?', $clientId);

        $results = $this->_db->fetchRow($select);
        return $this->buildClientModel($results);
    }

    public function getHouseholdersByClientId($clientId)
    {
        $select = $this->_db->select()
            ->from(array('m' => 'hmember'), array(
                'm.hmember_id',
                'm.first_name',
                'm.last_name',
                'm.relationship',
                'birthdate' => 'DATE_FORMAT(m.birthdate, "%m/%d/%Y")',
                'left_date' => 'DATE_FORMAT(m.left_date, "%m/%d/%Y")',
            ))
            ->join(
                array('h' => 'household'),
                'm.household_id = h.household_id',
                array()
            )
            ->where('h.current_flag = 1')
            ->where('h.mainclient_id = ? OR h.spouse_id = ?', $clientId, $clientId)
            ->order(array('m.last_name', 'm.first_name', 'm.hmember_id'));

        $results = $this->_db->fetchAssoc($select);
        return $this->buildHouseholderModels($results);
    }

    public function getEmployersByClientId($clientId)
    {
        $select = $this->_db->select()
            ->from(array('e' => 'employment'), array(
                'e.employment_id',
                'e.company',
                'e.position',
                'start_date' => 'DATE_FORMAT(e.start_date, "%m/%d/%Y")',
                'end_date' => 'DATE_FORMAT(e.end_date, "%m/%d/%Y")',
            ))
            ->where('e.client_id = ?', $clientId)
            ->order(array(
                'ISNULL(e.end_date) DESC',
                'e.end_date DESC',
                'e.start_date DESC',
                'e.employment_id',
            ));

        $results = $this->_db->fetchAssoc($select);
        return $this->buildEmployerModels($results);
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

        if ($dbResult['spouse_first_name'] !== null) {
            $spouse = new Application_Model_Impl_Client();
            $spouse->setId($dbResult['spouse_id'])
                   ->setFirstName($dbResult['spouse_first_name'])
                   ->setBirthDate($dbResult['spouse_birthdate']);
        } else {
            $spouse = null;
        }

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
            ->setSpouse($spouse)
            ->setCurrentAddr($addr)
            ->setDoNotHelpReason($dbResult['do_not_help_reason']);

        return $client;
    }

    private function buildHouseholderModels($dbResults)
    {
        $householders = array();

        foreach ($dbResults as $dbResult) {
            $householder = new Application_Model_Impl_Householder();
            $householder
                ->setId($dbResult['hmember_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setRelationship($dbResult['relationship'])
                ->setBirthDate($dbResult['birthdate'])
                ->setDepartDate($dbResult['left_date']);

            $householders[] = $householder;
        }

        return $householders;
    }

    private function buildEmployerModels($dbResults)
    {
        $employers = array();

        foreach ($dbResults as $dbResult) {
            $employer = new Application_Model_Impl_Employer();
            $employer
                ->setId($dbResult['employment_id'])
                ->setCompany($dbResult['company'])
                ->setPosition($dbResult['position'])
                ->setStartDate($dbResult['start_date'])
                ->setEndDate($dbResult['end_date']);

            $employers[] = $employer;
        }

        return $employers;
    }
}
