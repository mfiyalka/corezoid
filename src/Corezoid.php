<?php

namespace mfiyalka\corezoid;

/**
 * Class Corezoid
 *
 * @author  Mykhailo Fiialka <mfiyalka@gmail.com>
 * @package mfiyalka\corezoid
 */
class Corezoid
{
    /** @var string Host Corezoid */
    private $host = 'https://www.corezoid.com';

    /** @var string Version API */
    private $version = '1';

    /** @var string Format API */
    private $format = 'json';

    /** @var string Login */
    private $api_login;

    /** @var string Secret key */
    private $api_secret;

    /** @var array Array tasks */
    private $tasks = array();

    /**
     * Corezoid constructor
     *
     * @param int $api_login
     * @param string $api_secret
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($api_login, $api_secret)
    {
        if (empty($api_login)) {
            throw new \InvalidArgumentException('Login is empty');
        }
        if (empty($api_secret)) {
            throw new \InvalidArgumentException('Secret key is empty');
        }

        $this->api_login  = $api_login;
        $this->api_secret = $api_secret;
    }

    /**
     * Add new task
     *
     * @param string $reference
     * @param int $processID
     * @param array $data
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addTask($reference, $processID, $data = array())
    {
        if (empty($reference)) {
            throw new \InvalidArgumentException('Reference is empty');
        }
        if (empty($processID)) {
            throw new \InvalidArgumentException('ID process is empty');
        }

        $this->tasks[] = array(
            'ref'     => $reference,
            'type'    => 'create',
            'obj'     => 'task',
            'conv_id' => $processID,
            'data'    => $data
        );

        return $this;
    }

    /**
     * Send task to Corezoid
     *
     * @return mixed
     */
    public function sendTask()
    {
        $content = json_encode(array('ops' => $this->tasks));

        $time = time();

        $url = $this->makeUrl($time, $content);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $server_output = curl_exec($ch);
        curl_close($ch);

        return $server_output;
    }

    /**
     * Check Signature
     *
     * @param string $signature
     * @param string $time
     * @param string $content
     *
     * @return string
     */
    public function checkSignature($signature, $time, $content)
    {
        $make_sign = $this->makeSignature($time, $content);
        return ($signature == $make_sign) ? true : false;
    }

    /**
     * Create URL for Corezoid
     *
     * @param string $time
     * @param string $content
     * @return string
     */
    private function makeUrl($time, $content)
    {
        $sign = $this->makeSignature($time, $content);

        return $this->host.'/api/'
            .$this->version.'/'
            .$this->format.'/'
            .$this->api_login.'/'
            .$time.'/'
            .$sign;
    }

    /**
     * Create Signature
     *
     * @param string $time
     * @param string $content
     * @return string
     */
    private function makeSignature($time, $content)
    {
        return $this->str2hex(sha1($time.$this->api_secret.$content.$this->api_secret, 1));
    }

    /**
     * Convert string to HEX
     *
     * @param string $string
     * @return string
     */
    private function str2hex($string)
    {
        return array_shift(unpack('H*', $string));
    }
}
