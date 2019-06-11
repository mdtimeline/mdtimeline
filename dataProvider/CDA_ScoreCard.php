<?php


class CDA_ScoreCard
{

	private $server = 'https://sitenv.org/scorecard/';


	public function getScorePdf($ccda, $name){
		$this->server .= 'savescorecardservicebackend';
		$score = $this->getScore($ccda, $name);

		return $score;
	}

	public function getScoreData($ccda, $name){
		$this->server .= 'savescorecardservicebackend';
		$score = $this->getScore($ccda, $name);

		if($score !== false){
			$score = json_decode($score, true);
		}

		return $score;
	}

	public function getScoreDocument($ccda, $name){
		$score_document = $this->getScorePdf($ccda, $name);

		if($score_document !== false){
			// todo create temp document
			include_once (ROOT. '/dataProvider/DocumentHandler.php');
			$DocumentHandler = new DocumentHandler();
			return $DocumentHandler->createRawTempDocumentByNameAndDocument($name . '.pdf', $score_document);;
		}

		return false;
	}


	public function getScore($file, $file_name){

		$tmpfname = null;

		if(file_exists($file) && simplexml_load_file($file) !== false){

			$file_data = new CURLFile($file,'application/xml', $file_name);

		}elseif (simplexml_load_string($file) !== false){

			$tmpfname = tempnam(site_temp_path, "ccda-");
			$handle = fopen($tmpfname, "w");
			fwrite($handle, $file);
			fclose($handle);
			$file_data = new CURLFile($tmpfname,'application/xml', $file_name);

		}else{
			error_log('Invalid C-CDA XML ' . $file_name);
			return false;
		}

		$data['ccdaFile'] = $file_data;

		$ch = curl_init($this->server);

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data']
		]);

		$result = curl_exec($ch);

		if(isset($tmpfname)){
			unlink($tmpfname);
		}

		if($errno = curl_errno($ch)) {
			$error_message = curl_strerror($errno);
			error_log("cURL error ({$errno}):\n {$error_message}");
			curl_close($ch);
			return false;
		}

		curl_close($ch);
		return $result;
	}

}
