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
require_once (ROOT . '/lib/PHPMailer/PHPMailerAutoload.php');
include_once (ROOT . '/dataProvider/Globals.php');

class Email {

	private $EMAIL_METHOD;
	private $EMAIL_NOTIFICATION_HOUR;
	private $EMAIL_FROM_ADDRESS;
	private $SMS_GATEWAY_APIKEY;
	private $SMS_GATEWAY_PASSWORD;
	private $SMS_GATEWAY_USENAME;
	private $SMS_NOTIFICATION_HOUR;
	private $SMTP_HOST;
	private $SMTP_PASS;
	private $SMTP_PORT;
	private $SMTP_USER;

	private $API_ACCOUNT;
	private $API_KEY;

	private $API_STATUS_CHECK_DAYS = 1;

	private $AuditLog;

	/**
	 * @var bool|MatchaCUP
	 */
	private $t;

	/**
	 * @var bool|MatchaCUP
	 */
	private $tk;

	function __construct() {

		$this->t = MatchaModel::setSenchaModel('App.model.administration.EmailTemplate');
		$this->tk = MatchaModel::setSenchaModel('App.model.administration.EmailTracking');

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

		$this->API_ACCOUNT = Globals::getGlobal('API_ACCOUNT');
		$this->API_KEY = Globals::getGlobal('API_KEY');

		$this->AuditLog = new AuditLog();

	}

	/***
	 * @param $pid
	 * @param $eid
	 * @param $to_address
	 * @param $subject
	 * @param $from_email
	 * @param $from_name
	 * @param $body
	 * @param bool $audit_log
	 * @param null $facility_id
	 * @param array $attachments
	 * @param array $embedded_images
	 * @param null $bbc_bbc_recipients;
	 * @return array
	 * @throws Exception
	 */
	function Send($pid, $eid, $to_address, $subject, $from_email, $from_name, $body, $audit_log = true, $facility_id = null, $attachments = [], $embedded_images = [], $bcc_recipients = null, $cc_recipients = null){

		if($this->EMAIL_METHOD == 'SMTP'){
			return $this->SendSMTP($pid, $eid, $to_address, $subject, $from_email, $from_name, $body, $audit_log, $facility_id, $attachments, $embedded_images, $bcc_recipients, $cc_recipients);
		}elseif($this->EMAIL_METHOD == 'API'){
			return $this->SendAPI($pid, $eid, $to_address, $subject, $from_email, $from_name, $body, $audit_log, $facility_id, $attachments, $embedded_images, $bcc_recipients, $cc_recipients);
		}else{
			throw new Exception('Email: SMTP or API not configured');
		}
	}

	private function SendAPI($pid, $eid, $to_address, $subject, $from_email, $from_name, $body, $audit_log = true, $facility_id = 0, $attachments = [], $embedded_images = [], $bcc_recipients = null, $cc_recipients = null){


		if($this->API_ACCOUNT == '' || $this->API_KEY == ''){
			throw new Exception('Email: API not configured');
		}

		$message = [];

		if(is_string($to_address)){
			$message['recipients'][] = $to_address;
		}elseif(is_array($to_address)){
			if(isset($to_address[1])){
				$message['recipients'][] = $to_address[0];
			}elseif(isset($to_address[0])){
				$message['recipients'][] = $to_address[0];
			}
		}


        if(isset($bcc_recipients) && $bcc_recipients !== ''){
            $message['bcc'] = [];

            $bcc_recipients = is_string($bcc_recipients) ? explode(',', $bcc_recipients) : $bcc_recipients;
            foreach ($bcc_recipients as $bcc_recipient){
                if(filter_var($bcc_recipient, FILTER_VALIDATE_EMAIL)){
                    $message['bcc'][] = $bcc_recipient;
                }
            }

            if(count($message['bcc']) === 0){
                unset($message['bcc']);
            }

        }

        if(isset($cc_recipients) && $cc_recipients !== ''){
            $message['cc'] = [];

            $cc_recipients = is_string($cc_recipients) ? explode(',', $cc_recipients) : $cc_recipients;
            foreach ($cc_recipients as $cc_recipient){
                if(filter_var($cc_recipient, FILTER_VALIDATE_EMAIL)){
                    $message['cc'][] = $cc_recipients;
                }
            }

            if(count($message['cc']) === 0){
                unset($message['cc']);
            }

        }

		$tpl = $this->getMasterTemplate($facility_id);
		if($tpl !== false){

			$body = str_replace('[BODY]', $body, $tpl['body']);

			if(
				isset($tpl['from_address']) &&
				filter_var($tpl['from_address'], FILTER_VALIDATE_EMAIL) !== false
			){
				$from_address = $tpl['from_address'];
			}
		}

		if(isset($from_email) && filter_var($from_email, FILTER_VALIDATE_EMAIL) !== false){
			$from_address = $from_email;
		}

		if(isset($from_address)){
			$message['headers']['from'] = $from_address;
		}else{
			$message['headers']['from'] = $this->EMAIL_FROM_ADDRESS;
		}

		$message['headers']['subject'] = $subject;
		$message['content']['text/html'] = $body;


		if(file_exists(site_path . '/logo-email.png')){
			//$PHPMailer->addEmbeddedImage(site_path . '/logo-email.png', 'logo');
		}


		foreach($attachments as $attachment){

			$f = finfo_open();
			$mime_type = finfo_buffer($f, base64_decode($attachment['data']), FILEINFO_MIME_TYPE);
			finfo_close($f);

			$message['attachments'][] = [
				'fileName' => $attachment['filename'],
				'contentType' => $mime_type,
				'content' => $attachment['data'],
			];

		}


		$url = "https://api.paubox.net/v1/{$this->API_ACCOUNT}/messages";
	    $ch = curl_init($url);
	    $data = ['data' => ['message' => $message ]];
	    $payload = json_encode($data);

	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "Content-Type: application/json",
		    "Authorization: Token token={$this->API_KEY}"
	    ]);

		//execute the POST request
	    $result = curl_exec($ch);


	    $error_message = false;

	    if($errno = curl_errno($ch)) {
		    $error_message = curl_strerror($errno);
	    }

		//close cURL resource
	    curl_close($ch);

	    if($error_message !== false){
		    error_log('Email: ' . $error_message);
		    return [
			    'success' => false,
			    'error' => $error_message
		    ];
	    }

	    $result = json_decode($result, true);

	    if(isset($result['errors'])){

		    $error_message = [];
	    	foreach ($result['errors'] as $error){
			    $error_message[] = 'Code: ' . $error['code'] . ' Description: ' .  $error['title'] . ' - ' . $error['details'];
		    }

		    if(!empty($error_message)){

			    error_log('Email Errors: ' . implode(', ', $error_message));
			    error_log('Email Payload: ' . $payload);

			    return [
				    'success' => false,
				    'error' => implode(', ', $error_message)
			    ];
		    }
	    }

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
			'sourceTrackingId' => $result['sourceTrackingId'],
			'error' => ''
		];

	}

	private function SendSMTP($pid, $eid, $to_address, $subject, $from_email, $from_name, $body, $audit_log = true, $facility_id = null, $attachments = [], $embedded_images = [], $bcc_recipients = null, $cc_recipients = null){

		$PHPMailer = new PHPMailer();
		//$PHPMailer->SMTPDebug = 2;

		if($this->EMAIL_METHOD == 'SMTP'){
			$PHPMailer->isSMTP();                       // Set mailer to use SMTP
			$PHPMailer->Host = $this->SMTP_HOST;        // Specify main and backup SMTP servers
			$PHPMailer->SMTPAuth = true;                // Enable SMTP authentication
			$PHPMailer->Username = $this->SMTP_USER;    // SMTP username
			$PHPMailer->Password = $this->SMTP_PASS;    // SMTP password
			$PHPMailer->SMTPSecure = 'tls';             // Enable TLS encryption, `ssl` also accepted
			$PHPMailer->Port = 587; //$this->SMTP_PORT;        // TCP port to connect to

		}else{
			throw new Exception('Email: SMTP server not configured');
		}

		$PHPMailer->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$PHPMailer->isHTML(true);

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

			$body = str_replace('[BODY]', $body, $tpl['body']);

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

		if(isset($from_email) && filter_var($from_email, FILTER_VALIDATE_EMAIL) !== false){
			$from_address = $from_email;
		}

		if(!isset($from_name)){
			$from_name = '';
		}

		if(isset($from_address)){
			$PHPMailer->setFrom($from_address, $from_name);
		}else{
			$PHPMailer->setFrom($this->EMAIL_FROM_ADDRESS);
		}

		$PHPMailer->Subject = $subject;
		$PHPMailer->Body = $body;

		foreach($attachments as $attachment){
			if(isset($attachment['path']) && file_exists($attachment['path'])){
				$PHPMailer->addAttachment($attachment['path'], $attachment['filename']);
			}else{
				$data = base64_decode($attachment['data'], true) || $attachment['data'];
				$PHPMailer->addStringAttachment($data, $attachment['filename']);
			}
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

	private function getTemplateByType($facility_id, $template_type){
        $this->t->setOrFilterProperties(['facility_id']);
        $this->t->addFilter('facility_id', '0');
		if(isset($facility_id)){
			$this->t->addFilter('facility_id', $facility_id);
		}
		if(isset($template_type)){
			$this->t->addFilter('template_type', $template_type);
		}
		$this->t->addFilter('active', '1');
		return $this->t->load()->one();
	}

	public function generateEmail($facility_id, $template_type, $placeholders, $values){

		$tpl = $this->getTemplateByType($facility_id, $template_type);

		if($tpl !== false){
			$email_body = str_replace($placeholders, $values, $tpl['body']);
			return [
				'subject' => $tpl['subject'],
				'from_email' => $tpl['from_email'],
				'from_name' => $tpl['from_name'],
				'body' => $email_body
		        ];
		}

		return false;
	}

	public function CheckAPIEmails(){

		$sql = "SELECT * FROM email_tracking AS tk
				 WHERE tk.send_time > DATE_SUB(CURDATE(), INTERVAL {$this->API_STATUS_CHECK_DAYS} DAY)
				   AND (tk.delivery_status IS NULL OR tk.delivery_status = 'processing' OR tk.opened_status IS NULL OR tk.opened_status = 'unopened')";

		$emails = $this->tk->sql($sql)->all();

		foreach ($emails as $email) {
            $response = $this->CheckAPIEmail($email['source_tracking_id']);

            if (!$response['success']) {
                continue;
            }

            if (!isset($response['result']['data']['message']['message_deliveries'])) {
                continue;
            }

            $message_deliveries = $response['result']['data']['message']['message_deliveries'];
            $message_delivery = end($message_deliveries);

            if ($message_delivery === false) {
                error_log('CheckAPIEmail Error: $message_delivery === false');
                continue;
            }

            if (!isset($message_delivery['status'])) {
                error_log('CheckAPIEmail Error: $message_delivery->status not set');
                error_log(print_r($message_delivery, true));
                continue;
            }

            $email = [
                'id' => $email['id']
            ];

            if (isset($message_delivery['status']['deliveryStatus'])) {
                $email['delivery_status'] = $message_delivery['status']['deliveryStatus'];
            }
            if (isset($message_delivery['status']['deliveryTime'])) {
                $email['delivery_time'] = date('Y-m-d H:i:s', strtotime($message_delivery['status']['deliveryTime']));
            }
            if (isset($message_delivery['status']['openedStatus'])) {
                $email['opened_status'] = $message_delivery['status']['openedTime'];
            }
            if (isset($message_delivery['status']['openedTime'])) {
                $email['opened_time'] = date('Y-m-d H:i:s', strtotime($message_delivery['status']['openedTime']));
            }

            if(count($email) === 1){
                continue;
            }

            $this->tk->save((object) $email);

        }
	}

	public function CheckAPIEmail($sourceTrackingId){

		$url = "https://api.paubox.net/v1/{$this->API_ACCOUNT}/message_receipt?sourceTrackingId={$sourceTrackingId}";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Content-Type: application/json",
			"Authorization: Token token={$this->API_KEY}"
		]);

		//execute the POST request
		$result = curl_exec($ch);

		$error_message = false;

		if($errno = curl_errno($ch)) {
			$error_message = curl_strerror($errno);
		}

		//close cURL resource
		curl_close($ch);

		if($error_message !== false){
			error_log('Email: ' . $error_message);
			return [
				'success' => false,
				'error' => $error_message
			];
		}

		$result = json_decode($result, true);

		if(!isset($result)){
			return [
				'success' => false,
				'error' => 'Unable to json_decode email status result'
			];
		}

		return [
			'success' => true,
			'result' => $result
		];

	}
}
