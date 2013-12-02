PHP Punctis API
=======================
[![Build Status](https://travis-ci.org/Populissrl/punctis-api-php.png?branch=master)](https://travis-ci.org/Populissrl/punctis-api-php)


Introduction
------------
A PHP class wrapper for Punctis API

Installation
------------

Cloning by GitHub (recommended)
----------------------------
The recommended way to get a working copy of this project is to clone the repository from github: 

    cd my/project/dir
    git clone https://github.com/populissrl/punctis-api-php
    cd my/project/dir/punctis-api-php
    composer.phar install

Usage example
-------------

    include_once(dirname(__FILE__) . '/vendor/autoload.php');

    $options = array(
        //'demoMode' => true,
        'authMode' => 'safe',
        'authKey' => '0niLCwOdQYhrPcFkbUgA9S7eW',
        'brandCode' => '0JBR39VmzwGF'
    );
    $p = new \Populis\Punctis\Api($options);

    $catalog = $p->getCatalog();
