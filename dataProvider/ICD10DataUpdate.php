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

include_once(ROOT . '/classes/Time.php');
include_once(ROOT . '/classes/FileManager.php');

class ICD10DataUpdate
{
	private $db;
	private $codeType;
	private $installedRevision;
	private $error = false;

    /**
     * @var FileManager
     */
    private $FileManager;

	function __construct()
	{
		// Set the time limit to infinite, this will take a lot of time.
		// Maybe hours on a magnetic hard disk, maybe 1 hour to minutes in a SSD (Solid State Disk)
		set_time_limit(0);
        ini_set('memory_limit','-1');

        $this->FileManager = new FileManager();
	}
    public function updateCodes($revision){

        $codes_zip = "https://www.cms.gov/files/zip/{$revision}-code-descriptions-tabular-order.zip";
        $tabular_zip = "https://www.cms.gov/files/zip/{$revision}-code-tables-tabular-and-index.zip";

        $code_description_file = rtrim(site_temp_path, '/') . '/' . basename($codes_zip);
        $tabular_index_file = rtrim(site_temp_path, '/') . '/' . basename($tabular_zip);

        if(file_put_contents( $code_description_file, file_get_contents($codes_zip)) === false) {
            return [
                'success' => false,
                'message' => 'Unable to download ' . $codes_zip
            ];
        }

        if(file_put_contents( $tabular_index_file, file_get_contents($tabular_zip)) === false) {
            return [
                'success' => false,
                'message' => 'Unable to download ' . $tabular_zip
            ];
        }

        $result = $this->parseTabularIndexFile($code_description_file, $tabular_index_file, $revision, true);


        exec("rm -rf {$code_description_file}");
        exec("rm -rf {$tabular_index_file}");


        if(!$result['success']){
            return [
                'success' => false,
                'message' => 'parse Results Issue '
            ];
        }

        $conn = Matcha::getConn();

        foreach ($result['sql_scripts'] as $sql_script){
            $sth = $conn->prepare($sql_script);
            $sth = $sth->execute();
        }

        // Insert into Standard table
        $standard_insert_sql = "INSERT INTO standardized_tables_track (code_type, imported_date, revision_name, revision_number, revision_version, revision_date) 
                                VALUES (?,?,?,?,?,?)";

        $now = date('Y-m-d H:i:s');
        $date = date('Y-m-d', strtotime($now));

        $sth = $conn->prepare($standard_insert_sql);
        $sth = $sth->execute([
            'ICD10',
            $date,
            $tabular_index_file,
            $revision,
            $revision,
            $revision
        ]);


//        $sth = $this->conn->prepare('INSERT INTO `cvx_codes` (`cvx_code`, `name`, `description`, `note`, `status`, `update_date`)
//										  VALUES (?,?,?,?,?,?)');
//        foreach($xml as $cvx){
//            $sth->execute([
//                trim($cvx->CVXCode),
//                trim($cvx->ShortDescription),
//                trim($cvx->FullVaccinename),
//                trim($cvx->Notes),
//                trim($cvx->Status),
//                (date('Y-m-d H:i:s', strtotime($cvx->LastUpdated)))
//            ]);
//        }

        return [
            'success' => true,
            'message' => 'Successfully updated ICD10 Codes'
        ];
    }

    public function parseTabularIndexFile($code_description_file, $tabular_index_file, $revision, $valid_for_coding_only){

//        $revision = '2022';
//        $tabular_index_file = "/Users/mdtimeline/Sites/mdtimeline/sites/default/temp/{$revision}-Table-and-Index.zip";
//        $code_description_file = "/Users/mdtimeline/Sites/mdtimeline/sites/default/temp/{$revision}-Code-Descriptions.zip";
//        $sql_file_path = "/Users/mdtimeline/Sites/mdtimeline/sites/default/temp/ICD10-CM-{$revision}-FULL.sql";

        $index_directory = $this->FileManager->extractFileToTempDir($tabular_index_file);
        $code_directory = $this->FileManager->extractFileToTempDir($code_description_file);

        $index_path = scandir($index_directory)[2];
        $code_path = scandir($code_directory)[2];

        $index_xml = $index_directory . "/{$index_path}/icd10cm_tabular_{$revision}.xml";
        $order_txt = $code_directory . "/{$code_path}/icd10cm_order_{$revision}.txt";

        if(!file_exists($index_xml)){
            exec("rm -rf {$index_directory}");
            exec("rm -rf {$code_directory}");
            return [
                'success' => false,
                'error' => "icd10cm_eindex_{$revision}.xml not found"
            ];
        }

        if(!file_exists($order_txt)){
            exec("rm -rf {$index_directory}");
            exec("rm -rf {$code_directory}");
            return [
                'success' => false,
                'error' => "icd10cm_order_{$revision}.txt not found"
            ];
        }

        $index_xml = file_get_contents($index_xml);
        $xml = simplexml_load_string($index_xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $index_array = json_decode($json,true);

        $index = [];

        $this->indexDiag($index_array, $index);

        $order_txt = file_get_contents($order_txt);
        $lines = explode(PHP_EOL, $order_txt);

        $codes = [];

        foreach ($lines as $line){
            //$line = explode("\t", $line);

            $dx_code = trim(substr($line,6,7));
            $formatted_dx_code = isset($index[$dx_code]) ? $index[$dx_code] : null;

            if (!isset($dx_code)){

                // one position root
                $root_code = substr($dx_code, 0, -1);
                $root_code = isset($index[$root_code]) ? $index[$root_code] : null;

                if(isset($root_code)){
                    $formatted_dx_code = $root_code . substr($dx_code, -1);
                }else{

                    // two position root
                    $root_code = substr($dx_code, 0, -2);
                    $root_code = isset($index[$root_code]) ? $index[$root_code] : null;

                    if(isset($root_code)){
                        $formatted_dx_code = $root_code . substr($dx_code, -2);
                    }else{
                        $formatted_dx_code = $dx_code;
                    }

                }

            }

            $valid_for_coding = substr($line,14,1);

            if($valid_for_coding_only && $valid_for_coding !== '1'){
                continue;
            }

            $codes[] = [
                'dx_code' => $dx_code,
                'formatted_dx_code' =>  $formatted_dx_code,
                'valid_for_coding' => $valid_for_coding,
                'short_desc' => str_replace("'", "\'", trim(substr($line,16,60))),
                'long_desc' => str_replace("'", "\'", trim(substr($line,77,100000))),
                'active' => '1',
                'revision' => $revision,
            ];
        }

        $sql_scripts = [];
        //$sql_scripts[] = 'TRUNCATE `icd10_dx_order_code`;';

        $insert = 'INSERT INTO icd10_dx_order_code (`dx_code`, `formatted_dx_code`, `valid_for_coding`, `short_desc`, `long_desc`, `active`,`revision`) VALUES ';
        $code_chunks = array_chunk($codes, 1000);

        foreach ($code_chunks as $dx_codes){
            $values = [];
            foreach ($dx_codes as $dx_values){
                $values[] = "('" . implode("','", $dx_values). "')";
            }
            $sql_scripts[] = $insert . PHP_EOL . implode((',' . PHP_EOL), $values) . " ON DUPLICATE KEY UPDATE  revision='{$revision}';";
        }

//        unlink($sql_file_path);
//        file_put_contents($sql_file_path, $sql);

        exec("rm -rf {$index_directory}");
        exec("rm -rf {$code_directory}");

        return [
            'success' => true,
            'sql_scripts' => $sql_scripts
        ];

    }



    private function indexDiag($data, &$index){

        foreach ($data as $key => $vale){

            if(is_array($vale)){
                $this->indexDiag($vale, $index);
            }

            if($key === 'name'){
                $formatted = str_replace('.', '', $vale);
                $index[$formatted] = $vale;
            }

        }

    }

}

//
//$f = new ExternalDataUpdate();
//print '<pre>';
//$params = new stdClass();
//$params->codeType = 'HCPCS';
//$params->version = 2013;
//$params->basename = '13anweb.zip';
//$params->path = 'C:\inetpub\wwwroot\gaiaehr\contrib\hcpcs\13anweb.zip';
//$f->updateCodes($params);
