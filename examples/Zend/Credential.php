<?php
/**
 * This class encapsulates the activities of hashing and validating hashed
 * passwords.
 *
 * Usage:
 *
 * Instantiate the class and set the salt value using setSalt().  Then you may
 * use the crypt() or isValid() methods.
 *
 * If you are generating a hash for a new password, you can use the
 * generateSalt() method to create a salt string for you, which can be passed
 * to setSalt().
 *
 * @author zircote
 */
class MyProject_Auth_Credential
{
    /**
     * The encryption algorithm to use.  Default is '$2a$', for Blowfish.
     *
     * @var string
     */
    protected $algorithm = '$2a$';

    /**
     * The number of rounds the encryption algorithm should use. Default is
     * '13$'.
     *
     * @var string
     */
    protected $load = '16$';

    /**
     * The current salt value used during encryption.
     *
     * @var string
     */
    protected $salt;

    /**
     * Generate a new salt string and return it.  Does not modify state.
     *
     * @return string
     */
    public function generateSalt()
    {
        return substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set the current salt value to be used during encryption.
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Encrypt the given plaintext password using this object's specified
     * encryption algorithm, number of rounds, and salt.  Returns the encrypted
     * value.
     *
     * @param string $password
     *
     * @return string
     */
    public function crypt($password)
    {
        return crypt(
            $password,
            $this->algorithm . $this->load . $this->getSalt()
        );
    }

    /**
     * Given a plaintext password and hashed password, does the plaintext
     * password match the hashed password?
     *
     * @param string $password
     * @param string $credential
     *
     * @return boolean
     */
    public function isValid($password, $credential)
    {
        return ($credential == crypt($password, substr($credential, 0, 29)));
    }
}

