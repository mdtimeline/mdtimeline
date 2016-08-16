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

if(!isset($_SESSION)){
    session_cache_limiter('private');
    session_name('mdTimeLine');
    session_start();
}
if(!defined('_GaiaEXEC')){
	define('_GaiaEXEC', 1);
	require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/registry.php');
}
if(!isset($_REQUEST['token']) || str_replace(' ', '+', $_REQUEST['token']) != $_SESSION['user']['token']) die('Not Authorized!');

include_once(ROOT . '/sites/' . $_REQUEST['site'] . '/conf.php');
include_once(ROOT . '/classes/MatchaHelper.php');
include_once (ROOT. '/dataProvider/Patient.php');
include_once (ROOT. '/dataProvider/CCDDocument.php');

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 172800); // 2 days
ini_set('zlib.output_compression',0);
ini_set('implicit_flush',1);
ob_end_clean();
set_time_limit(0);

class DataPortability {

	private $Patient;

	function __construct(){
		$this->Patient = new Patient();

	}

	function __destruct() {

	}

	function export($params = null){

		$this->sendPregress('Export Started...<br>');

		$this->sendPregress('Getting Patients...<br>');

		$patients = $this->Patient->getPatients($params, false);
		unset($this->Patient);

		$this->sendPregress('Patients Count: '. count($patients) . '<br>');

		$zip = new ZipArchive();
		$file = site_temp_path . '/mdtimeline-patients-export-'. time() .'.zip';

		$this->sendPregress('Zip file created: '. $file . '<br>');

		if($zip->open($file, ZipArchive::CREATE) !== true){
			throw new Exception("cannot open <$file>");
		}
		$zip->addFromString('cda2.xsl', file_get_contents(ROOT . '/lib/CCRCDA/schema/cda2.xsl'));


		$this->sendPregress('Exporting...<br>');

		foreach($patients as $i => $patient){
			$patient = (object) $patient;
			$CCDDocument = new CCDDocument();
			$CCDDocument->setPid($patient->pid);
			$CCDDocument->createCCD();
			$CCDDocument->setTemplate('toc');
			$CCDDocument->createCCD();
			$ccd = $CCDDocument->get();
			unset($CCDDocument);
			$zip->addFromString($patient->pid . '-patient-cda' . '.xml', $ccd);
			unset($patients[$i], $ccd);

			if(($i % 100) === 0 && $i !== 0){
				$this->sendPregress(' ' . $i . '<br>');
			}else{
				$this->sendPregress('.');
			}
		}

		$zip->close();

		header('Location: ' . $file);

//		header('Content-Type: application/zip');
//		header('Content-Length: ' . filesize($file));
//		header('Content-Disposition: attachment; filename="' . $file . '"');
//		readfile($file);
	}

	private function sendPregress($progress){
		print $progress;
		ob_flush();
		flush();
	}
}

ob_start();

header('Content-type: text/html; charset=utf-8');
header('Content-Encoding: none;');//disable apache compressed

$D = new DataPortability();
$D->export();
