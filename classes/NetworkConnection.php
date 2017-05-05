<?php

/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class NetworkConnection
{
    /**
     * @var
     */
    private $Port;

    /**
     * @var
     */
    private $Host;

    /**
     * @var bool
     */
    private $Secure;

    /**
     * @var bool
     */
    private $VerifyCertificate;

    /**
     * @var
     */
    private $Protocol;

    /**
     * @var bool
     */
    private $RequestType;

    /**
     * @var bool
     */
    private $Payload;

    /**
     * NetworkConnection constructor.
     * The class boot instructions.
     */
    function __construct(){
        $this->Secure = false;
        $this->VerifyCertificate = false;
        $this->Protocol = false;
        $this->Payload = false;
        $this->RequestType = false;
        $this->Port = false;
    }

    public function setRequestType($type){
        if(isset($type)){
            $type = strtoupper($type);
            switch($type){
                case 'SOAP':
                    $this->RequestType = 'SOAP';
                    break;
            }
        } else {
            $this->RequestType = false;
        }
    }

    /**
     * Set the network protocol to be used if the proto is not in the list
     * just put it in false
     * @param $proto
     */
    public function setProtocol($proto){
        if(isset($proto)){
            $proto = strtoupper($proto);
            switch($proto){
                case 'HTTP':
                    $this->Protocol = (string)$proto;
                    break;
                case 'HTTPS':
                    $this->Protocol = (string)$proto;
                    break;
                case 'FTP':
                    $this->Protocol = (string)$proto;
                    break;
                default:
                    $this->Protocol = false;
                    break;
            }
        } else {
            $this->Protocol = false;
        }

    }

    /**
     * Just set the port of the designated Host
     * @param $port
     */
    public function setPort($port){
        if(isset($port) || is_numeric($port)){
            $this->Port = (int)$port;
        } else {
            $this->Port = 0;
        }
    }

    /**
     * Just set the Host for the connection, the host could be FTP, HTTP, HTTPS, SMTP, POP3s
     * @param $host
     */
    public function setHost($host){
        if(isset($host)){
            $this->Host = (string)$host;
        } else {
            $this->Host = '';
        }
    }

    /**
     * Tells if the connection to be established or in between is secured or not.
     * @return bool
     */
    public function isSecured(){
        return $this->Secure;
    }

    /**
     * Tellls if the connection to be established will check for a valid certificate
     * @return bool
     */
    public function isVerifying(){
        return $this->VerifyCertificate;
    }

    /**
     * Set the connection to be established will be secure of not.
     * @param $secure
     * @param $verify
     */
    public function setSecure($secure, $verify){
        if(isset($secure) || is_bool($secure)){
            $this->Secure = $secure;
        } else {
            $this->Secure = false;
        }
        if(isset($verify) || is_bool($verify)){
            $this->VerifyCertificate = $verify;
        } else {
            $this->VerifyCertificate = false;
        }
    }

    public function setPayload($payload){
        if(isset($payload) && !empty($payload)){
            $this->Payload = $payload;
        } else {
            $this->Payload = false;
        }
    }

    public function sendPayload($payload){
        try {
            switch($this->RequestType){
                case 'SOAP':
                    return $this->viaHTTP_SOAP($payload['action'], $payload['request']);
                    break;
            }
        } catch (\Exception $Error) {
            return $Error->getMessage();
        }
    }

    private function viaHTTP_SOAP($action, $request){
        try {
            ini_set('soap.wsdl_cache_enabled', 0);
            ini_set('soap.wsdl_cache_ttl', 0);

            if(isset($this->port)){
                $wsdl = 'http://' . $this->host . ':' . $this->port;
            } else {
                $wsdl = 'http://' . $this->host;
            }
            $client = new \SoapClient($wsdl, array(
                "trace" => true,
                "exception" => true
            ));

            return $client->{$action}($request);
        } catch (\Exception $Error) {
            return $Error->getMessage();
        }
    }

}
