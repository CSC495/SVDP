<?php

/**
 * Implements a safe means of passing data unmodified across form submissions.
 *
 * HTTP is a stateless protocol, and so it's useful to have a mechanism for passing arbitrary data
 * between across multiple form submissions. One such mechanism is PHP's built-in serialization
 * support; however, reading serialized data directly from the client affords no protection against
 * data modification by malicious end users.
 *
 * One solution, the one implemented by this class, is to salt the serialized data with a secret,
 * server-only string, hashing the result. If both the serialized data and its hash are transmitted
 * to the client, then the code that processes the next form submission can check the serialized
 * data against the hash again---if the two do not match, then client-side tampering took place.
 *
 * Note: This class returns serialized strings with base64 encoding applied so that they can be
 * stored directly in hidden form fields.
 */
class App_Service_SafeSerialize
{

    /**
     * Cached binary string containing the current session's secret salt.
     *
     * @string
     */
    private $_salt;

    /**
     * Constructs a new `App_Service_SafeSerialize` object, generating a new random salt for this
     * session if necessary.
     */
    public function __construct()
    {
        $session = new Zend_Session_Namespace('Svdp\App\Service\SafeSerialize');

        if (isset($session->salt)) {
            $this->_salt = $session->salt;
        } else {
            $this->_salt = $session->salt = self::generateSalt();
        }
    }

    /**
     * Given a PHP value, returns an array containing the base64-encoded serialized version of that
     * value (returned with key `serial`), as well as a salted hash of the value (returned with key
     * `hash`).
     *
     * @param mixed $x
     * @return string[]
     */
    public function serialize($x)
    {
        $ret           = array();
        $ret['serial'] = base64_encode(serialize($x));
        $ret['hash']   = sha1($this->_salt . $ret['serial']);

        return $ret;
    }

    /**
     * Given a base64-encoded serialized PHP value and the salted hash of the value, returns the 
     * original value.
     *
     * @param string $serial
     * @param string $hash
     * @return mixed
     * @throws InvalidArgumentException if the serialized string doesn't match its hash, indicating
     * client-side modification of the serialized data
     */
    public function unserialize($serial, $hash)
    {
        if (sha1($this->_salt . $serial) !== $hash) {
            throw new InvalidArgumentException("Serialized string doesn't match hash.");
        }

        return unserialize(base64_decode($serial));
    }

    /**
     * Generates and returns a random salt as a binary string.
     *
     * @return string
     */
    private static function generateSalt()
    {
        return mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
    }
}
