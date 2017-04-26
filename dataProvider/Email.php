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

	private $t;

	function __construct() {

		$this->t = MatchaModel::setSenchaModel('App.model.administration.EmailTemplate');

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
	 * @param int         $pid
	 * @param int         $eid
	 * @param string      $to_address
	 * @param string      $subject
	 * @param string      $body
	 * @param bool        $audit_log
	 * @param int         $facility_id
	 * @param array       $attachments
	 * @param array       $embedded_images
	 *
	 * @return array
	 * @throws Exception
	 */
	function Send($pid, $eid, $to_address, $subject, $body, $audit_log = true, $facility_id = null, $attachments = [], $embedded_images = []){

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

		$PHPMailer->clearAddresses();
		$PHPMailer->clearAllRecipients();
		$PHPMailer->clearAttachments();
		$PHPMailer->clearBCCs();
		$PHPMailer->clearCCs();
		$PHPMailer->clearReplyTos();
		$PHPMailer->clearCustomHeaders();

		if(is_string($to_address)){
			$PHPMailer->addAddress($to_address);
		}elseif(is_array($to_address)){
			if(isset($to_address[1])){
				$PHPMailer->addAddress($to_address[0], $to_address[1]);
			}elseif(isset($to_address[0])){
				$PHPMailer->addAddress($to_address[0]);
			}
		}

		$tpl = $this->getMasterTemplate($facility_id);
		if($tpl !== false){

			$body = str_replace('[BODY]', $body, $tpl);

			if(
				isset($tpl['from_address']) &&
				filter_var($tpl['from_address'], FILTER_VALIDATE_EMAIL) !== false
			){
				$from_address = $tpl['from_address'];
			}
		}

		if(file_exists(site_path . '/logo-email.png')){
			$PHPMailer->addEmbeddedImage(site_path . '/logo-email.png', 'logo');
		}

		if(isset($from_address)){
			$PHPMailer->setFrom($from_address);
		}else{
			$PHPMailer->setFrom($this->EMAIL_FROM_ADDRESS);
		}

		$PHPMailer->Subject = $subject;
		$PHPMailer->Body = $body;

		foreach($attachments as $attachment){
			$PHPMailer->addStringAttachment($attachment['data'], $attachment['filename']);
		}

		foreach($embedded_images as $embedded_image){
			$PHPMailer->addStringEmbeddedImage($embedded_image['data'], $embedded_image['cid']);
		}

		if(!$PHPMailer->send()) {
			error_log('Email: ' . $PHPMailer->ErrorInfo);

			return [
				'success' => false,
				'error' => $PHPMailer->ErrorInfo
			];

		}else{

			if($audit_log){
				$log = new stdClass();
				$log->pid = $pid;
				$log->eid = $eid;
				$log->event = 'SEND';
				$log->event_description = 'Email Sent To: ' . $to_address;
				$this->AuditLog->addLog($log);
			}
			return [
				'success' => true,
				'error' => ''
			];
		}
	}

	private function getMasterTemplate($facility_id){
		$this->t->setOrFilterProperties(['facility_id']);
		$this->t->addFilter('facility_id', '0');
		if(isset($facility_id)){
			$this->t->addFilter('facility_id', $facility_id);
		}
		$this->t->addFilter('template_type', 'master');
		$this->t->addFilter('active', '1');
		return $this->t->load()->sortBy('facility_id', 'DESC')->one();
	}
}