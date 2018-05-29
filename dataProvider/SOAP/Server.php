<?php

/**
 *
 * SOAP SERVER v.0.1
 *
 * @file
 * Provides a simple SOAP server for demo purposes
 *
 */

if(!defined('_GaiaEXEC')) define('_GaiaEXEC', 1);
include_once ('../../registry.php');

include_once('SoapHandler.php');
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

$server = new SoapServer(URL . '/dataProvider/SOAP/wsdl.php?wsdl');
$server->setClass('SoapHandler');
$server->handle();
