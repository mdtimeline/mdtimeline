<?php

/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2012 Ernesto Rodriguez
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

include_once(ROOT . '/dataProvider/DocumentHandler.php');
include_once(ROOT . '/classes/Utils.php');

class Printer
{


    /**
     * @var bool|\MatchaCUP
     */
    private $p;


    function __construct()
    {
        $this->p = \MatchaModel::setSenchaModel('App.model.administration.Printer');

    }

    public function getPrinters()
    {
        $printers = $this->p->load()->all();
        return $printers;
    }

    public function getPrinter($params)
    {
        $printer = $this->p->load()->one($params);
        return $printer;
    }

    public function addPrinter($params)
    {
        return $this->p->save($params);
    }

    public function updatePrinter($params)
    {
        return $this->p->save($params);
    }

    public function destroyPrinter($params)
    {
        return $this->p->destroy($params);
    }

    public function doTempDocumentPrint($printer_id, $temp_document_id)
    {

        $DocumentHandler = new DocumentHandler();
        $document_record = $DocumentHandler->getTempDocument(['id' => $temp_document_id], true);

        error_log(print_r(base64_decode($document_record['document']), true));

        $this->doPrint($printer_id, $document_record['document']);

        unset($DocumentHandler, $document_record);

    }

    public function doDocumentPrint($printer_id, $document_id)
    {
        $DocumentHandler = new DocumentHandler();
        $document_record = $DocumentHandler->getPatientDocument(['id' => $document_id], true, true);
        $this->doPrint($printer_id, $document_record['document']);
        unset($DocumentHandler, $document_record);
    }

    public function doPrint($printer_id, $document)
    {

        $printer = $this->p->load(['id' => $printer_id])->one();

        if ($printer === false) {
            return [
                'success' => false,
                'error' => 'Printer not found'
            ];
        }

        if (!file_exists(site_temp_path) || !is_writable(site_temp_path)) {
            return [
                'success' => false,
                'error' => 'Document temp directory issue'
            ];
        }

        if (!Utils::isBinary($document)) {
            $document = base64_decode($document);
        }

        $temp_file_path = site_temp_path . '/' . uniqid('print_');

        if (!file_put_contents($temp_file_path, $document, FILE_USE_INCLUDE_PATH)) {
            error_log('Print document could not be saved on this location. {$tem_fname}');
            return [
                'success' => false,
                'error' => 'Print document could not be saved'
            ];
        }

        if ($printer['printer_protocol'] == 'ipp') {

            require_once(ROOT . '/lib/php-ipp/PrintIPP.php');

            $ipp = new \PrintIPP();
            $ipp->setLog(null, 0, 0);

            $ipp->setHost($printer['printer_host']);

            if (isset($printer['printer_port']) && $printer['printer_port'] != '') {
                $ipp->setPort($printer['printer_port']);
            }

            $ipp->setPrinterURI($printer['printer_uri']);

            if (
                isset($printer['printer_user']) && $printer['printer_user'] != '' &&
                isset($printer['printer_pass'])
            ) {
                $ipp->setAuthentication($printer['printer_user'], $printer['printer_pass']);
            }

            $info = new finfo(FILEINFO_MIME_TYPE);
            $media_type = $info->buffer($document);
            $ipp->setMimeMediaType($media_type);
            $ipp->setData($temp_file_path);
            $s = $ipp->printJob();

            print 'DEBUG...';
            print '<pre>';

            print $ipp->getDebug();
            print '<pre/>';

        } else {

            /**
             * LPR basic print command
             */


            $printer_name = $printer['printer_name'];

            if (!isset($printer_name) || empty($printer_name)) {
                error_log('Printer name is not set');
                return [
                    'success' => false,
                    'error' => 'Printer name is not set'
                ];
            }

            $command_args = [];

            $command_args[] = "lp -d {$printer_name}";

            if (isset($printer['size']) && !empty($printer['size'])) $command_args[] = "-o media={$printer['size']}";
            if (isset($printer['landscape']) && $printer['landscape'] === true) $command_args[] = '-o landscape';
            if ((isset($printer['fit_to_page']) && $printer['fit_to_page'] === true) || !isset($printer['fit_to_page'])) $command_args[] = '-o fit-to-page';
            if (isset($printer['one_side']) && $printer['one_side'] === true) $command_args[] = '-o sides=one-sided';
            if (isset($printer['two_side']) && $printer['two_side'] === true && $printer['one_side'] === false) {
                if (isset($printer['landscape']) && $printer['landscape'] === true) {
                    $command_args[] = '-o sides=two-sided-short-edge';
                } else {
                    $command_args[] = '-o sides=two-sided-long-edge';
                }
            }

            $command_args[] = $temp_file_path;

            $command = implode(' ', $command_args);

            shell_exec($command);
        }

        unlink($temp_file_path);

        return [
            'success' => true,
            'error' => ''
        ];

    }

    public function getStatus($printer_id)
    {
        $printer = $this->p->load(['id' => $printer_id])->one();

        if ($printer === false) {
            return [
                'success' => false,
                'error' => 'Printer not found'
            ];
        }

        if ($printer['printer_protocol'] == 'ipp') {

            require_once(ROOT . '/lib/php-ipp/PrintIPP.php');

            $ipp = new \PrintIPP();
            //$ipp->debug_level = 0;
            $ipp->setLog(null, 0, 0);
            $ipp->setHost($printer['printer_host']);
            if (isset($printer['printer_port']) && $printer['printer_port'] != '') {
                $ipp->setPort($printer['printer_port']);
            }
            $ipp->setPrinterURI($printer['printer_uri']);
            if (
                isset($printer['printer_user']) && $printer['printer_user'] != '' &&
                isset($printer['printer_pass'])
            ) {
                $ipp->setAuthentication($printer['printer_user'], $printer['printer_pass']);
            }


            $ipp->getPrinterAttributes();

            echo "Getting Jobs: " . $ipp->getJobs($my_jobs = true, $limit = 0, "completed", true) . "<br />";
            echo "Job 0 state: " . $ipp->jobs_attributes->job_0->job_state->_value0 . "<br />";
            echo "Job 0 state-reasons: " . $ipp->jobs_attributes->job_0->job_state_reasons->_value0 . "<br />";
            echo "<pre>";
            print_r($ipp->jobs_attributes);
            echo "</pre>";

        }
    }

    public function testPrint($printer_id)
    {

        $printer = $this->p->load(['id' => $printer_id])->one();

        if ($printer === false) {
            return [
                'success' => false,
                'error' => 'Printer not found'
            ];
        }

        if ($printer['printer_protocol'] == 'ipp') {

            require_once(ROOT . '/lib/php-ipp/PrintIPP.php');

            $ipp = new \PrintIPP();
            //$ipp->debug_level = 0;
            $ipp->setLog(null, 0, 0);
            $ipp->setHost($printer['printer_host']);

            if (isset($printer['printer_port']) && $printer['printer_port'] != '') {
                $ipp->setPort($printer['printer_port']);
            }

            $ipp->setPrinterURI($printer['printer_uri']);

            if (
                isset($printer['printer_user']) && $printer['printer_user'] != '' &&
                isset($printer['printer_pass'])
            ) {
                $ipp->setAuthentication($printer['printer_user'], $printer['printer_pass']);
            }

            $ipp->setMimeMediaType("text/plain");
            $ipp->setData('TEST PAGE');
            $ipp->setRawText();
            $ipp->printJob();

            print '<pre>';
            print $ipp->getDebug();
            print '<pre/>';

        } else {
            /**
             * LPR basic print command
             */
            $printer = $printer['printer_name'];
//			$pdf = '/Library/WebServer/Documents/gaiaehr/resources/templates/default.pdf';
            //$command = "/usr/bin/lpr -P {$printer} {$pdf} 2>&1";
            //print shell_exec($command);

//			$data = base64_decode('TEST PAGE');
//			$handles = array(
//				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
//				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
//				2 => array("pipe", "a")   // stderr is a file to write to
//			);
//			// Setting of $server, $printer_name, $options_flag omitted...
//			$command = '/usr/bin/lpr -P ' . $printer;
//			$pipes = array();
//			$process = proc_open($command, $handles, $pipes);
//
//			if(is_resource($process)){
//				// $pipes now looks like this:
//				// 0 => writeable handle connected to child stdin
//				// As we've been given data to write directly, let's kinda like do that.
//				fwrite($pipes[0], $data);
//				fclose($pipes[0]);
//				// 1 => readable handle connected to child stdout
//				$stdout = fgets($pipes[1]);
//				fclose($pipes[1]);
//				// 2 => readable handle connected to child stderr
//				$stderr = fgets($pipes[2]);
//				fclose($pipes[2]);
//				// It is important that you close any pipes before calling
//				// proc_close in order to avoid a deadlock
//				$return_value = proc_close($process);
//			}
        }

    }

}
