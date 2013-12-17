<?php

use Populis\Punctis\Api;

class Punctis_ApiTest extends PHPUnit_Framework_TestCase
{

    // {{{ getInstance
    /**
     * @test
     */
    public function getInstance()
    {
        $options = array(
            'authMode' => 'safe',
            'authKey' => 'fweòfmwEWFWE783e3',
            'brandCode' => '0JBdfkn68ddK'
        );
        $p = new Api($options);
        $this->assertInstanceOf('Populis\Punctis\Api', $p);
    }
    // }}}

    // {{{ getOptionsError
    /**
     * @test
     * @expectedException Populis\Punctis\Api\Exception
     */
    public function getOptionsError()
    {
        $options = array(
            //'authMode' => 'safe',
            'authKey' => 'fweòfmwEWFWE783e3',
            'brandCode' => '0JBdfkn68ddK'
        );
        $p = new Api($options);
    }
    // }}}

    // {{{ setAndGetCurlProperties
    /**
     * @test
     */
    public function setAndGetCurlProperties()
    {
        $options = array(
            'authMode' => 'safe',
            'authKey' => 'fweòfmwEWFWE783e3',
            'brandCode' => '0JBdfkn68ddK'
        );
        $p = new Api($options);
        $optName = 'testOption';
        $optVal = 'testOptionValue';
        $p->setCurlOption($optName, $optVal);
        $this->assertEquals($p->getCurlOption($optName), $optVal);
    }
    // }}}

    // {{{ _getApiOptions
    private function _getApiOptions()
    {
        return array (
            'authMode' => 'safe',
            'authKey' => '0niLCwOdQYhrPcFkbUgA9S7eW',
            'brandCode' => '0JBR39VmzwGF'
        );
    }
    // }}}

    // {{{ checkUser
    /**
     * @test
     */
    public function checkUserReturnValues()
    {
        $options = $this->_getApiOptions();
        $p = new Api($options);

        $tests = array (
            // email-address => return value
            'email-address@not-exist.it' => 2, // do not exists
            'grisou77@gmail.com' => 1, // exists on punctis db but not authorized
            //'it.software@populis.com' => 0 // exists on puntis db and has authorized
            );
        foreach ( $tests as $email => $expectation ) {
            $ret = $p->checkUser($email);
            $this->assertEquals($ret, $expectation);    
        }
    }
    // }}}

}
