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

}
