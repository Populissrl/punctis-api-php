<?php

namespace Populis\Punctis;
use Populis\Punctis\Api\Exception as Exception;
use Populis\Punctis\Api\Arguments;

class Api
{
    private $authMode = null;
    private $authKey = null;
    private $brandCode = null;
    private $command = null;
    private $demoMode = false;
    private $debugMode = false;
    private $serverUrl = null;
    private $internalCallData = array(); 
    private $curlOpt = array();

    // {{{ __construct
    /**
     * get a Populis\Punctis\Api object instance
     * @access public
     * @param array $options
     * @return Popusli\Punctis\Api instance
     */
    public function __construct($options)
    {
        $this->_setDefaultOptions($options);
        $this->serverUrl = 'http://punctis.com/api';
        if ( $this->demoMode ) {
            $this->serverUrl = 'http://demodev.punctis.it/api';
        }
        $this->curlOpt[CURLOPT_CONNECTTIMEOUT] = 5;
        $this->curlOpt[CURLOPT_TIMEOUT] = 15;
        $this->curlOpt[CURLOPT_DNS_CACHE_TIMEOUT] = 120;
    }
    // }}}

    // {{{ _setDefaultOptions
    /**
     * set default options and check required options
     * @access private
     * @param array $options
     */
    private function _setDefaultOptions($opts)
    {
        $defaults = array(
            'demoMode'  => false,
            'debugMode'  => false
        );
        $requiredOptions = array(
            'demoMode'  => 'is_bool',
            'debugMode' => 'is_bool',
            'authMode'  => 'is_string',
            'authKey'   => 'is_string',
            'brandCode' => 'is_string',
        );
        foreach ( $defaults as $opt => $val ) {
            if ( !array_key_exists($opt, $opts) ) {
                $opts[$opt] = $val;
            }
        }
        foreach ( $requiredOptions as $opt => $checkFunc ) {
            if ( !array_key_exists($opt, $opts) ) {
                throw new Exception("Missing parameter {$opt}");
            }
            if ( !$checkFunc($opts[$opt])) {
                throw new Exception("Parameter's type wrong for {$opt}");
            }
            $this->$opt = $opts[$opt];
        }
    }
    // }}}

    // {{{ _isValidCommand
    /**
     * check il the given command is valid
     * @access private
     * @param string $command
     * @return bool
     */
    private function _isValidCommand($command)
    {
        $validCommands = array(
            'TMPsetUserr',
            'checkUser',
            'getCatalog',
            'getLegal',
            'getScore',
            'ping',
            'redemPrice',
            'setPoints',
            'setUser',
        );
        return in_array($command, $validCommands);
    }
    // }}}

    // {{{ __call
    /**
     * invoke an API method
     * @access public
     * @params mix $args
     * @return JSON
     */
    public function __call($name, $args)
    {
        $this->command = $name;
        if ( !$this->_isValidCommand($this->command) ) {
            throw new Exception("You requested an invalid command: {$this->command}");
        }
        $this->internalCallData = array( 
            'auth-key' => $this->authKey,
            'auth-mode' => $this->authMode, 
            'brandcode' => $this->brandCode, 
            'arguments' => json_encode($args),
            'command' => $name
        );
        if ( $this->debugMode ) {
            var_dump($this->internalCallData);
        }
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->serverUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->internalCallData));
            foreach( array_keys($this->curlOpt) as $propertyName) {
                curl_setopt($ch, $propertyName, $this->curlOpt[$propertyName]);
            }
            $output = curl_exec($ch);
            if ( $this->debugMode ) {
                $info = curl_getinfo($ch);
                var_dump($info); 
                var_dump($output); 
            }
            curl_close($ch);
            return json_decode($output);
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }
    // }}}

    // {{{ setCurlOption
    /**
     * set a curl option
     * @access public
     * @param int $optName the option name
     * @param mix $optVal the option value
     */
    public function setCurlOption($optName, $optVal)
    {
        $this->curlOpt[$optName] = $optVal;
    }
    // }}}

    // {{{ getCurlOption
    /**
     * get a curl option
     * @access public
     * @param int $optName the option name
     * @return mix the option value
     */
    public function getCurlOption($optName)
    {
        if ( !array_key_exists($optName, $this->curlOpt) ) {
            throw new Exception("Trying to get a not existent property {$optName} from curl options.");
        }
        return $this->curlOpt[$optName];
    }
    // }}} 

    // {{{ getScore
    /**
     * get the user score
     * 
     * @access public
     * @param string $email
     * @return int
     */
    public function getScore($email)
    {
        $args = new Arguments();
        $args->username = $email;
        $ret = 0;
        $response = $this->__call('getScore', $args);
        if ( $response->code == 1 ) {
            if ( $response->response->score ) {
                return $response->response->score;
            }
            return $ret;
        }
        throw new Exception($response->response->description);
    }
    // }}}

    // {{{ getLegal
    /**
     * get the legals
     * 
     * @access public
     * @param string $lang default 'it'
     * @return int
     */
    public function getLegal($lang = 'it')
    {
        $args = new Arguments();
        $args->lang = $lang;
        $response = $this->__call('getLegal', $args);
        if ( $response->code == 1 ) {
            return $response->response->legal;
        }
        throw new Exception($response->response->description);
    }
    // }}}

    // {{{ checkUser
    /**
     * check the user
     * 
     * @access public
     * @param string $email
     * @return int
     */
    public function checkUser($email)
    {
        $args = new Arguments();
        $args->username = $email;
        $response = $this->__call('checkUser', $args);
        if ( $response->code == 1 ) {
            if ( $response->response->indb == 1 && $response->response->auth == 1 ) {
                return 0;
            } elseif ( $response->response->indb == 1 ) {
                return 1;
            } 
            return 2;
        }
        throw new Exception($response->response->description);
    }
    // }}}

}
