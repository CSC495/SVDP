<?php

class App_Service_SafeSerialize
{

    private $_salt;

    public function __construct()
    {
        $session = new Zend_Session_Namespace('Svdp\App\Service\SafeSerialize');

        if (isset($session->salt)) {
            $this->_salt = $session->salt;
        } else {
            $this->_salt = $session->salt = self::generateSalt();
        }
    }

    public function serialize($x)
    {
        $ret           = array();
        $ret['serial'] = base64_encode(serialize($x));
        $ret['hash']   = sha1($this->_salt . $ret['serial']);

        return $ret;
    }

    public function unserialize($serial, $hash)
    {
        if (sha1($this->_salt . $serial) !== $hash) {
            throw new InvalidArgumentException("Serialized string doesn't match hash.");
        }

        return unserialize(base64_decode($serial));
    }

    private static function generateSalt()
    {
        return mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
    }
}
