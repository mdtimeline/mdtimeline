<?php

/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
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

include_once(ROOT . '/dataProvider/AuditLog.php');

class Email {

	private $EMAIL_METHOD;
	private $EMAIL_NOTIFICATION_HOUR;
	private $SMS_GATEWAY_APIKEY;
	private $SMS_GATEWAY_PASSWORD;
	private $SMS_GATEWAY_USENAME;
	private $SMS_NOTIFICATION_HOUR;
	private $SMTP_HOST;
	private $SMTP_PASS;
	private $SMTP_PORT;
	private $SMTP_USER;

	private $AuditLog;

	function __construct() {

		$this->EMAIL_METHOD = Globals::getGlobal('EMAIL_METHOD');
		$this->EMAIL_NOTIFICATION_HOUR = Globals::getGlobal('EMAIL_NOTIFICATION_HOUR');
		$this->EMAIL_FROM_ADDRESS = Globals::getGlobal('EMAIL_FROM_ADDRESS');
		$this->SMS_GATEWAY_APIKEY = Globals::getGlobal('SMS_GATEWAY_APIKEY');
		$this->SMS_GATEWAY_PASSWORD = Globals::getGlobal('SMS_GATEWAY_PASSWORD');
		$this->SMS_GATEWAY_USENAME = Globals::getGlobal('SMS_GATEWAY_USENAME');
		$this->SMS_NOTIFICATION_HOUR = Globals::getGlobal('SMS_NOTIFICATION_HOUR');
		$this->SMTP_HOST = Globals::getGlobal('SMTP_HOST');
		$this->SMTP_PASS = Globals::getGlobal('SMTP_PASS');
		$this->SMTP_PORT = Globals::getGlobal('SMTP_PORT');
		$this->SMTP_USER = Globals::getGlobal('SMTP_USER');

		$this->AuditLog = new AuditLog();

	}

	/**
	 * @param int $pid
	 * @param int $eid
	 * @param string $to_address
	 * @param string $subject
	 * @param string $body
	 * @param null|string $from_address
	 * @param bool $audit_log
	 * @throws Exception
	 */
	function Send($pid, $eid, $to_address, $subject, $body, $from_address = null, $audit_log = true){

		$PHPMailer = new PHPMailer();

		if($this->EMAIL_METHOD == 'SMTP'){
			$PHPMailer->isSMTP();                       // Set mailer to use SMTP
			$PHPMailer->Host = $this->SMTP_HOST;        // Specify main and backup SMTP servers
			$PHPMailer->SMTPAuth = true;                // Enable SMTP authentication
			$PHPMailer->Username = $this->SMTP_USER;    // SMTP username
			$PHPMailer->Password = $this->SMTP_PASS;    // SMTP password
			$PHPMailer->SMTPSecure = 'tls';             // Enable TLS encryption, `ssl` also accepted
			$PHPMailer->Port = $this->SMTP_PORT;        // TCP port to connect to

		}else{
			throw new Exception('Email: SMTP server not configured');
		}

		if(is_string($to_address)){
			$PHPMailer->addAddress($to_address);
		}elseif(is_array($to_address)){
			if(isset($to_address[1])){
				$PHPMailer->addAddress($to_address[0], $to_address[1]);
			}elseif(isset($to_address[0])){
				$PHPMailer->addAddress($to_address[0]);
			}
		}

		if(isset($from_address)){
			$PHPMailer->setFrom($from_address);
		}else{
			$PHPMailer->setFrom($this->EMAIL_FROM_ADDRESS);
		}

		$PHPMailer->Subject = $subject;
		$PHPMailer->Body = $body;

		if(!$PHPMailer->send()) {
			error_log('Email: ' . $PHPMailer->ErrorInfo);
		}else{

			if($audit_log){
				$log = new stdClass();
				$log->pid = $pid;
				$log->eid = $eid;
				$log->event = 'SEND';
				$log->event_description = 'Email Sent To: ' . $to_address;
				$this->AuditLog->addLog($log);
			}
		}
	}
}