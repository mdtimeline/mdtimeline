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

include_once(ROOT . '/dataProvider/Patient.php');
include_once(ROOT . '/dataProvider/Person.php');
include_once(ROOT . '/dataProvider/PatientContacts.php');
include_once(ROOT . '/dataProvider/User.php');
include_once(ROOT . '/dataProvider/Encounter.php');
include_once(ROOT . '/dataProvider/Referrals.php');
include_once(ROOT . '/dataProvider/ReferringProviders.php');
include_once(ROOT . '/dataProvider/Facilities.php');
include_once(ROOT . '/dataProvider/DocumentFPDI.php');
include_once(ROOT . '/dataProvider/i18nRouter.php');

class Documents
{
    /**
     * @var MatchaHelper
     */
    private $db;

    /**
     * @var Patient
     */
    private $Patient;

    /**
     * @var Encounter
     */
    private $Encounter;

    /**
     * @var User
     */
    private $User;

    /**
     * @var ReferringProviders
     */
    private $ReferringProviders;

    /**
     * @var DocumentFPDI
     */
    public $pdf;

    /**
     * @var \MatchaCUP
     */
    private $t;

    /**
     * @var \MatchaCUP
     */
    private $h;
    private $ic;
    private $pi;
    private $fac;
    private $ref;
    private $sp;
    private $dp;

    /**
     * @var int
     */
    private $max_order_rows = 99;

    /**
     * @var array
     */
    private $encounter = null;

    private $columns_enabled;

    function __construct()
    {
        $this->db = new MatchaHelper();
        $this->Patient = new Patient();
        $this->Encounter = new Encounter();
        $this->User = new User();
        $this->ReferringProviders = new ReferringProviders();

        $this->t = \MatchaModel::setSenchaModel('App.model.administration.DocumentsPdfTemplate');
        $this->h = \MatchaModel::setSenchaModel('App.model.administration.DocumentsTemplatesHeaderFooter');


    }

    public function getArrayWithTokensNeededByDocumentID($id)
    {
        $this->db->setSQL("SELECT title, body FROM documents_templates WHERE id = '$id' ");
        $record = $this->db->fetchRecord(PDO::FETCH_ASSOC);
        $regex = '(\[\w*?\])';
        $body = $record['body'];
        preg_match_all($regex, $body, $tokensfound);
        return $tokensfound[0];
    }

    public function getTemplateBodyById($id)
    {
        $this->db->setSQL("SELECT title, body, max_order_items_per_page FROM documents_templates WHERE id = '$id' ");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }

    public function getAllPatientData($pid)
    {
        $this->db->setSQL("SELECT * FROM patient WHERE pid = '$pid'");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }

    public function updateDocumentsTitle(stdClass $params)
    {
        $data = get_object_vars($params);
        $id = $data['id'];
        unset($data['id'], $data['date']);
        $this->db->setSQL($this->db->sqlBind($data, 'patient_documents', 'U', ['id' => $id]));
        $this->db->execLog();
        return $params;
    }

    public function setArraySizeOfTokenArray($tokens)
    {
        $givingValuesToTokens = [];
        foreach ($tokens as $tok) {
            array_push($givingValuesToTokens, '');
        }
        return $givingValuesToTokens;
    }

    public function get_EncounterTokensData($params, $allNeededInfo, $tokens)
    {

        $enc_params = new stdClass();
        $enc_params->eid = $params->eid;
        $eid = $params->eid;
        $encounter = $this->Encounter->getEncounter($enc_params);

        if (!isset($encounter['encounter'])) {
            return $allNeededInfo;
        }

        $encounterCodes = $this->Encounter->getEncounterCodes($enc_params);

        $vitals = end($encounter['encounter']['vitals']);

        $soap = $encounter['encounter']['soap'];

        if (isset($encounter['encounter']['reviewofsystemschecks'])) {
            $rosCks = $encounter['encounter']['reviewofsystemschecks'];

            unset($rosCks['id'], $rosCks['pid'], $rosCks['eid'], $rosCks['uid'], $rosCks['date']);

            foreach ($rosCks as $rosc => $num) {
                if ($num == '' || $num == null || $num == 0) {
                    unset($rosCks[$rosc]);
                }
            }
        }

        if (isset($encounter['encounter']['reviewofsystems'])) {
            $reviewofsystems = $encounter['encounter']['reviewofsystems'];

            unset($reviewofsystems['pid'], $reviewofsystems['eid'], $reviewofsystems['uid'], $reviewofsystems['id'], $reviewofsystems['date']);

            foreach ($reviewofsystems as $ros => $num) {
                if ($num == '' || $num == null || $num == 'null') {
                    unset($reviewofsystems[$ros]);
                }
            }
        }

        $cpt = [];
        $dx = [];
        $hcpc = [];
        $cvx = [];

        if (isset($encounterCodes['rows'])) {
            foreach ($encounterCodes['rows'] as $code) {
                if ($code['code_type'] == 'CPT') {
                    $cpt[] = $code;
                } elseif ($code['code_type'] == 'ICD' || $code['code_type'] == 'ICD9' || $code['code_type'] == 'ICD10') {
                    $dx[] = $code;
                } elseif ($code['code_type'] == 'HCPC') {
                    $hcpc[] = $code;
                } elseif ($code['code_type'] == 'CVX') {
                    $cvx[] = $code;
                }
            }
        }

        $dx_records = $this->Encounter->getEncounterDxs(['eid' => $eid]);
        foreach ($dx_records as $dx_record) {
            $dx[] = $dx_record['code'];
        }

        if (isset($params->dx_required) && empty($dx)) {
            throw new Exception('Encounter Diagnosis Required');
        }

        $Medications = new Medications();
        $medications = $Medications->getPatientMedicationsByEid($eid);
        unset($Medications);

        $Immunizations = new Immunizations();
        $immunizations = $Immunizations->getImmunizationsByEid($eid);
        unset($Immunizations);

        $Allergies = new Allergies();
        $allergies = $Allergies->getPatientAllergiesByEid($eid);
        unset($Allergies);

        $ActiveProblems = new ActiveProblems();
        $activeProblems = $ActiveProblems->getPatientActiveProblemByEid($eid);
        unset($ActiveProblems);

        $encounter = $encounter['encounter'];

        $encounterInformation = [
            '[ENCOUNTER_DATE]' => $this->dateToString($encounter['service_date']),
            '[ENCOUNTER_START_DATE]' => $this->dateToString($encounter['service_date']),
            '[ENCOUNTER_END_DATE]' => $this->dateToString($encounter['close_date']),
            '[ENCOUNTER_BRIEF_DESCRIPTION]' => $encounter['brief_description'],
            '[ENCOUNTER_SENSITIVITY]' => $encounter['priority'],
            '[ENCOUNTER_WEIGHT_LBS]' => $vitals !== false ? $vitals['weight_lbs'] : '',
            '[ENCOUNTER_WEIGHT_KG]' => $vitals !== false ? $vitals['weight_kg'] : '',
            '[ENCOUNTER_HEIGHT_IN]' => $vitals !== false ? $vitals['height_in'] : '',
            '[ENCOUNTER_HEIGHT_CM]' => $vitals !== false ? $vitals['height_cm'] : '',
            '[ENCOUNTER_BP_SYSTOLIC]' => $vitals !== false ? $vitals['bp_systolic'] : '',
            '[ENCOUNTER_BP_DIASTOLIC]' => $vitals !== false ? $vitals['bp_diastolic'] : '',
            '[ENCOUNTER_PULSE]' => $vitals !== false ? $vitals['pulse'] : '',
            '[ENCOUNTER_RESPIRATION]' => $vitals !== false ? $vitals['respiration'] : '',
            '[ENCOUNTER_TEMP_FAHRENHEIT]' => $vitals !== false ? $vitals['temp_f'] : '',
            '[ENCOUNTER_TEMP_CELSIUS]' => $vitals !== false ? $vitals['temp_c'] : '',
            '[ENCOUNTER_TEMP_LOCATION]' => $vitals !== false ? $vitals['temp_location'] : '',
            '[ENCOUNTER_OXYGEN_SATURATION]' => $vitals !== false ? $vitals['oxygen_saturation'] : '',
            '[ENCOUNTER_HEAD_CIRCUMFERENCE_IN]' => $vitals !== false ? $vitals['head_circumference_in'] : '',
            '[ENCOUNTER_HEAD_CIRCUMFERENCE_CM]' => $vitals !== false ? $vitals['head_circumference_cm'] : '',
            '[ENCOUNTER_WAIST_CIRCUMFERENCE_IN]' => $vitals !== false ? $vitals['waist_circumference_in'] : '',
            '[ENCOUNTER_WAIST_CIRCUMFERENCE_CM]' => $vitals !== false ? $vitals['waist_circumference_cm'] : '',
            '[ENCOUNTER_BMI]' => $vitals !== false ? $vitals['bmi'] : '',
            '[ENCOUNTER_BMI_STATUS]' => $vitals !== false ? $vitals['bmi_status'] : '',
            '[ENCOUNTER_SUBJECTIVE]' => (isset($soap['subjective']) ? $soap['subjective'] : ''),
            '[ENCOUNTER_OBJECTIVE]' => (isset($soap['objective']) ? $soap['objective'] : ''),
            '[ENCOUNTER_ASSESSMENT]' => (isset($soap['assessment']) ? $soap['assessment'] : ''),
            '[ENCOUNTER_PLAN]' => (isset($soap['plan']) ? $soap['plan'] : ''),
            '[ENCOUNTER_CPT_CODES]' => $this->tokensForEncountersList($cpt, 1),
            '[ENCOUNTER_ICD_CODES]' => implode(' ', $dx),
            '[ENCOUNTER_HCPC_CODES]' => $this->tokensForEncountersList($hcpc, 3),
            '[ENCOUNTER_ALLERGIES_LIST]' => $this->tokensForEncountersList($allergies, 4),
            '[ENCOUNTER_MEDICATIONS_LIST]' => $this->tokensForEncountersList($medications, 5),
            '[ENCOUNTER_ACTIVE_PROBLEMS_LIST]' => $this->tokensForEncountersList($activeProblems, 6),
            '[ENCOUNTER_IMMUNIZATIONS_LIST]' => $this->tokensForEncountersList($immunizations, 7),
            //'[ENCOUNTER_PREVENTIVECARE_DISMISS]' => $this->tokensForEncountersList($preventivecaredismiss, 10),
            '[ENCOUNTER_REVIEWOFSYSTEMSCHECKS]' => isset($rosCks) ? $this->tokensForEncountersList($rosCks, 11) : '',
            '[ENCOUNTER_REVIEWOFSYSTEMS]' => isset($reviewofsystems) ? $this->tokensForEncountersList($reviewofsystems, 12) : '',
            //            '[]'     =>$this->tokensForEncountersList($hcpc,13),
            //            '[]'     =>$this->tokensForEncountersList($hcpc,14),
            //            '[]'     =>$this->tokensForEncountersList($hcpc,15),
            //            '[]'
            // =>$this->tokensForEncountersList($preventivecaredismiss,16),
            //            '[]'
            // =>$this->tokensForEncountersList($reviewofsystemschecks,17),
            //            '[]'
            // =>$this->tokensForEncountersList($preventivecaredismiss,16),
            //            '[]'
            // =>$this->tokensForEncountersList($preventivecaredismiss,16)
        ];

        foreach ($tokens as $i => $tok) {
            if (isset($encounterInformation[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $encounterInformation[$tok];
            }
        }

        $progress_key = array_search('[ENCOUNTER_PROGRESS_NOTE]', $tokens);
        if ($progress_key !== false) {
            $allNeededInfo[$progress_key] = $this->Encounter->ProgressNoteString(
                $this->Encounter->getProgressNoteByEid($enc_params->eid, false)
            );
        }

        $this->encounter = $encounter;

        return $allNeededInfo;
    }

    private function tokensForEncountersList($Array, $typeoflist)
    {
        $html = '';
        if ($typeoflist == 1) {
            $html .= '<table>';
            $html .= "<tr><th>" . "CPT" . "</th><th>" . "Code text" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['code'] . "</td><td>" . $row['code_text_short'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 2) {
            $html .= '<table>';
            $html .= "<tr><th>" . "ICD" . "</th><th>" . "Code text" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['code'] . "</td><td>" . $row['code_text'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 3) {
            $html .= '<table>';
            $html .= "<tr><th>" . "HCPC" . "</th><th>" . "Code text" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['code'] . "</td><td>" . $row['code_text'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 4) {
            $html .= '<table>';
            $html .= "<tr><th>" . "Allergies" . "</th><th>" . "Type" . "</th><th>" . "Severity" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['allergy'] . "</td><td>" . $row['allergy_type'] . "</td><td>" . $row['severity'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 5) {
            $html .= '<table>';
            $html .= "<tr><th>" . "Medications" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['STR'] . ' ' . $row['dose'] . ' ' . $row['route'] . ' ' . $row['form'] . ' ' . $row['directions'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 6) {
            $html .= '<table>';
            $html .= "<tr><th>" . "Active Problems" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['code_text'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 7) {
            $html .= '<table>';
            $html .= "<tr><th>" . "Immunizations" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['vaccine_name'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 8) {
            // Dental
        } elseif ($typeoflist == 9) {
            // Surgeries
        } elseif ($typeoflist == 10) {
            $html .= '<table>';
            $html .= "<tr><th>" . "Preventive Care" . "</th><th>" . "Reason" . "</th></tr>";
            foreach ($Array as $row) {
                $html .= "<tr><td>" . $row['description'] . "</td><td>" . $row['reason'] . "</td></tr>";
            }
            $html .= '</table>';
        } elseif ($typeoflist == 11) {
            $html .= '<table width="300">';
            $html .= "<tr><th>" . "Review of systems checks" . "</th><th>" . "Active?" . "</th></tr>";
            foreach ($Array as $key => $val) {
                $html .= "<tr><td>" . str_replace('_', ' ', $key) . "</td><td>" . (($val === 1 || $val === '1') ? 'Yes' : 'No') . "</td></tr>";
            }
            $html .= '</table>';

        } elseif ($typeoflist == 12) {
            $html .= '<table width="300">';
            $html .= "<tr><th>" . "Review of systems" . "</th><th>" . "Active?" . "</th></tr>";
            foreach ($Array as $key => $val) {
                $html .= "<tr><td>" . str_replace('_', ' ', $key) . "</td><td>" . (($val == 1 || $val == '1') ? 'Yes' : 'No') . "</td></tr>";
            }
            $html .= '</table>';

        }

        return ($Array == null || $Array == '') ? '' : $html;
    }

    private function getCurrentTokensData($allNeededInfo, $tokens)
    {
        $user_name = isset($_SESSION['user']) && isset($_SESSION['user']['name']) ?
            $_SESSION['user']['name'] : '';

        $currentInformation = [
            '[CURRENT_DATE]' => date('F j, Y'),
            '[CURRENT_USER_NAME]' => $user_name,
            '[CURRENT_USER_FULL_NAME]' => $user_name,
            '[CURRENT_USER_LICENSE_NUMBER]',
            '[CURRENT_USER_DEA_LICENSE_NUMBER]',
            '[CURRENT_USER_DM_LICENSE_NUMBER]',
            '[CURRENT_USER_NPI_LICENSE_NUMBER]',
            '[LINE]' => '<hr>'
        ];
        foreach ($tokens as $i => $tok) {
            if (isset($currentInformation[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $currentInformation[$tok];
            }
        }
        return $allNeededInfo;
    }

    private function getClinicTokensData($allNeededInfo, $tokens)
    {
        $facility = new Facilities();
        $facilityInfo = $facility->getActiveFacilitiesById($_SESSION['user']['facility']);
        $clinicInformation = [
            '[FACILITY_NAME]' => $facilityInfo['name'],
            '[FACILITY_PHONE]' => $facilityInfo['phone'],
            '[FACILITY_FAX]' => $facilityInfo['fax'],
            '[FACILITY_STREET]' => $facilityInfo['address'],
            '[FACILITY_STREET_CONT]' => $facilityInfo['address_cont'],
            '[FACILITY_CITY]' => $facilityInfo['city'],
            '[FACILITY_STATE]' => $facilityInfo['state'],
            '[FACILITY_POSTALCODE]' => $facilityInfo['postal_code'],
            '[FACILITY_COUNTRYCODE]' => $facilityInfo['country_code'],
            '[FACILITY_FEDERAL_EIN]' => $facilityInfo['ein'],
//			'[FACILITY_SERVICE_LOCATION]' => $facilityInfo['service_location'],
            '[FACILITY_BILLING_LOCATION]' => $facilityInfo['billing_location'],
            '[FACILITY_FACILITY_NPI]' => $facilityInfo['npi']
        ];
        unset($facility);
        foreach ($tokens as $i => $tok) {
            if (isset($clinicInformation[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $clinicInformation[$tok];
            }
        }
        return $allNeededInfo;
    }

    public function mergeDocuments($files_paths)
    {
        include_once(ROOT . '/classes/Utils.php');
        $os = Utils::getOS();

        //command example: pdftk file1.pdf file2.pdf file3.pdf cat output out.pdf

        $command_args = [];

        $pdftk_location = Globals::getGlobal('pdftk_location');

        if ($pdftk_location === false || empty($pdftk_location)) {
            error_log('Global setting pdftk_location is not set');
            return false;
        }

        $command_args[] = $pdftk_location;

        $command_args = array_merge($command_args, $files_paths);

        $outputMergedDocumentPath = site_temp_path . '/' . uniqid('mergedDocument_') . '.pdf';

        $command_args[] = "cat output {$outputMergedDocumentPath} uncompress";

        $command = implode(" ", $command_args);

        $result = exec($command);

        if ($result != '') {
            error_log("Merge could not be done. Command return: {$result}");
            return false;
        }

        $mergedDocumentData = file_get_contents($outputMergedDocumentPath, true);

        if ($mergedDocumentData === false) {
            error_log("file_get_content failed on {$outputMergedDocumentPath}");
            unlink($outputMergedDocumentPath);
            return false;
        }

        unlink($outputMergedDocumentPath);

        return $mergedDocumentData;
    }

    /**
     * @param $params
     * @param string $path
     * @param null $custom_header_data
     * @param null $custom_footer_data
     * @param string $water_mark
     * @param array $key_images
     * @param array $key_images_config
     * @param null $pdf_format
     * @param array $mail_cover_info
     * @param int|null $template_id
     * @return bool|string
     */
    public function PDFDocumentBuilder($params, $path = '', $custom_header_data = null, $custom_footer_data = null, $water_mark = '', $key_images = [], $key_images_config = [], $pdf_format = null, $mail_cover_info = [], $template_id = null)
    {
        $pid = $params->pid;
        $regex = '(\[\w*?\]|\[\/\w*?\])';

        $pdf = new DocumentFPDI();
        $tokens = [];
        $header_data = [];
        $footer_data = [];
        $force_txt = isset($params->force_txt) && $params->force_txt === true;

        if (!empty($mail_cover_info)) {
            $pdf->CreateCover($mail_cover_info);
        }


        $pdf->water_mark = $water_mark;

        if (isset($custom_header_data)) {
            $pdf->addCustomHeaderData($custom_header_data);
        }
        if (isset($custom_footer_data)) {
            $pdf->addCustomFooterData($custom_footer_data);
        }

        if (isset($template_id)) {
            $template = $this->getPdfTemplateByFacilityId($params->facility_id, $pdf_format);
        }else if (isset($params->facility_id)) {
            $template = $this->getPdfTemplateByFacilityId($params->facility_id, $pdf_format);
        } elseif (isset($_SESSION['user']) && isset($_SESSION['user']['facility'])) {
            $template = $this->getPdfTemplateByFacilityId($_SESSION['user']['facility'], $pdf_format);
        } else {
            $template = $this->getPdfTemplateByFacilityId(null, $pdf_format);
        }

        // get header/footer data
        if (isset($params->templateId)) {
            $header_footer_lines = $this->h->load(['template_id' => $params->templateId])->all();

            if (!empty($header_footer_lines)) {
                foreach ($header_footer_lines as $line) {
                    preg_match_all('(\[\w*?\])', $line['text'], $line_tokens);
                    $tokens = array_merge($tokens, $line_tokens[0]);
                }

            }
        }

        // template
        if (file_exists($template['template']) && !is_dir($template['template'])) {
            $pdf->setSourceFile($template['template']);
        }

        $margins = [
            'left' => isset($template['body_margin_left']) ? intval($template['body_margin_left']) : 25,
            'top' => isset($template['body_margin_top']) ? intval($template['body_margin_top']) : 25,
            'right' => isset($template['body_margin_right']) ? intval($template['body_margin_right']) : 25,
            'bottom' => isset($template['body_margin_bottom']) ? intval($template['body_margin_bottom']) : 25,
            'footer_margin' => isset($template['footer_margin']) ? intval($template['footer_margin']) : 0
        ];

        $font = [
            'family' => isset($template['body_font_family']) ? $template['body_font_family'] : 'times',
            'size' => isset($template['body_font_size']) ? intval($template['body_font_size']) : 12,
            'style' => isset($template['body_font_style']) ? $template['body_font_style'] : '',
        ];

        $format = isset($template['format']) ? $template['format'] : 'LETTER';
        $encoding = isset($template['encoding']) ? $template['encoding'] : 'UTF-8';

        if (isset($template['header_body_space'])) {
            $pdf->setHeaderBodySpace($template['header_body_space']);
        }

        $tagvs = array(
            'b' => [['h' => 0, 'n' => 0], 1 => ['h' => 0, 'n' => 0]],
            'p' => [['h' => 0, 'n' => 0], 1 => ['h' => 0, 'n' => 0]],
//            'br' => [['h' => .7, 'n' => .7], ['h' => .7, 'n' => .7]],
            'ul' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'ol' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'div' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'table' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'th' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'tr' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'td' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'h1' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'h2' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'h4' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'h5' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]],
            'h6' => [['h' => 0, 'n' => 0], ['h' => 0, 'n' => 0]]
        );
        $pdf->setHtmlVSpace($tagvs);
        $pdf->setListIndentWidth(10);

        if (isset($params->custom_font_family)) {
            $font['family'] = $params->custom_font_family;
        }
        if (isset($params->custom_font_size)) {
            $font['size'] = $params->custom_font_size;
        }
        if (isset($params->custom_font_style)) {
            $font['style'] = $params->custom_font_style;
        }


        $pdf->setCustomHeaderLine(isset($template['header_line']) ? $template['header_line'] : false);
        $pdf->SetDefaultMonospacedFont('courier');
        $pdf->setHeaderMargin(0);
        $pdf->setFooterMargin($margins['footer_margin']);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetEncoding($encoding);

        /**
         * courier : Courier
         * courierB : Courier Bold
         * courierBI : Courier Bold Italic
         * courierI : Courier Italic
         * helvetica : Helvetica
         * helveticaB : Helvetica Bold
         * helveticaBI : Helvetica Bold Italic
         * helveticaI : Helvetica Italic
         * symbol : Symbol
         * times : Times New Roman
         * timesB : Times New Roman Bold
         * timesBI : Times New Roman Bold Italic
         * timesI : Times New Roman Italic
         * zapfdingbats : Zapf Dingbats
         */
        $font_path = ROOT . '/resources/fonts/' . $font['family'] . '.ttf';

        if (file_exists($font_path)) {
            $fontname = TCPDF_FONTS::addTTFfont($font_path);
            $pdf->SetFont($fontname, $font['style'], $font['size'], true);
        } else {
            $pdf->SetFont($font['family'], $font['style'], $font['size'], true);
        }

        $pdf->SetAutoPageBreak(true, $margins['bottom']);
        $pdf->SetMargins($margins['left'], $margins['top'], $margins['right'], true);

        $pdf->SetCreator('MDTIMELINE');
        $pdf->SetAuthor(isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'mdTimeline Automated Generator');

        if (isset($params->DoctorsNote)) {
            $body = $params->DoctorsNote;
            preg_match_all($regex, $body, $tokensfound);
            $tokens = array_merge($tokens, $tokensfound);
        } elseif (isset($params->templateId)) {
            $tokensfound = $this->getArrayWithTokensNeededByDocumentID($params->templateId);
            $tokens = array_merge($tokens, $tokensfound);
            //getting the template
            $body = $this->getTemplateBodyById($params->templateId);
        } else {
            $body['body'] = '';

            if (isset($params->body)) {
                $body['body'] = $params->body;
                preg_match_all($regex, $body['body'], $tokensfound);
                $tokens = array_merge($tokens, $tokensfound[0]);
            }
        }

        if (isset($body) && is_array($body) && isset($body['max_order_items_per_page'])) {
            $max_items = intval($body['max_order_items_per_page']);

            if ($max_items > 0) {
                $this->max_order_rows = $max_items;
            }
        }

        if (isset($tokens) && count($tokens) > 0) {

            $allNeededInfo = $this->setArraySizeOfTokenArray($tokens);
            $allNeededInfo = $this->get_PatientTokensData($pid, $allNeededInfo, $tokens);

            if (isset($params->eid) && $params->eid != 0 && $params->eid != '') {
                $allNeededInfo = $this->get_EncounterTokensData($params, $allNeededInfo, $tokens);
            }

            $allNeededInfo = $this->getCurrentTokensData($allNeededInfo, $tokens);
            $allNeededInfo = $this->getClinicTokensData($allNeededInfo, $tokens);
            if (isset($params->orderItems) || isset($params->date_ordered)) {
                $allNeededInfo = $this->parseTokensForOrders($params, $tokens, $allNeededInfo);
            }
            if (isset($params->referralId)) {
                $allNeededInfo = $this->addReferralData($params, $tokens, $allNeededInfo);
            }
            if (isset($params->docNoteid)) {
                $allNeededInfo = $this->addDoctorsNoteData($params, $tokens, $allNeededInfo);
            }
            if (isset($params->provider_uid)) {
                $allNeededInfo = $this->addProviderData($params, $tokens, $allNeededInfo);
            }
            if (isset($params->disclosure)) {
                $allNeededInfo = $this->getDisclosureTokensData($params->disclosure, $allNeededInfo, $tokens);
            }
            if (isset($this->encounter)) {
                $allNeededInfo = $this->addReferringProviderData($this->encounter, $tokens, $allNeededInfo);
            }

            $allNeededInfo = $this->getFormatTokensData($params->pid, $allNeededInfo, $tokens);

            if (isset($header_footer_lines)) {
                foreach ($header_footer_lines as $line) {
                    $line['text'] = str_replace($tokens, $allNeededInfo, $line['text']);

                    if ($line['data_type'] == 'HEADER') {
                        $header_data[] = $line;
                    } elseif ($line['data_type'] == 'FOOTER') {
                        $footer_data[] = $line;
                    }
                }

                if (!empty($header_data)) {
                    $pdf->addCustomHeaderData($header_data);
                }

                if (!empty($footer_data)) {
                    $pdf->addCustomFooterData($footer_data);
                }

                unset($header_data, $footer_data);
            }
        }

        $pdf->SetFont($font['family'], $font['style'], $font['size'], true);

        // add line token
        $tokens[] = '{line}';
        $allNeededInfo[] = '<hr style="margin: 10px;">';
        $html = str_replace($tokens, $allNeededInfo, (isset($params->DoctorsNote)) ? $body : $body['body']);

        $groups = explode('{newgroup}', $html);

        foreach ($groups as $group) {

            $pdf->startPageGroup();

            $pages = explode('{newpage}', $group);

            foreach ($pages as $page) {
                $pdf->AddPage('', $format, true);

                if ($this->isHtml($page)) {
                    //$pdf->writeHTML($page);
                    $pdf->writeHTMLCell(0, 0, '', '', $page);
                } else {
//					$pdf->writeHTML(nl2br($page));
                    $pdf->writeHTMLCell(0, 0, '', '', nl2br($page));
                }
            }
        }

        $pdf->SetY($pdf->GetY() + 5);

        $imagesCount = count($key_images);


        if ($imagesCount > 0) {

            if ($imagesCount > count($key_images_config)) {
                $config = end($key_images_config);
            } elseif (isset($key_images_config[$imagesCount])) {
                $config = $key_images_config[$imagesCount];
            } else {
                $config = ['cols' => 1, 'padding' => 2];
            }

            $imagesCols = $config['cols'];
            $imagesPadding = $config['padding'];

            if ($imagesCount < $imagesCols) {
                $imagesCols = $imagesCount;
            }

            $pageWidth = $pdf->getPageWidth();
            $bodyWidth = $pageWidth - $margins['left'] - $margins['right'];
            $imageWidth = ($bodyWidth / $imagesCols);

            //image //title
            foreach ($key_images as $index => $key_image) {

                if (!isset($key_image['image']) || !isset($key_image['title'])) {
                    continue;
                }

                $img = 'data://text/plain;base64,' . $key_image['image'];

                $lastCol = !(($index + 1) % $imagesCols);

                if ($lastCol) {
                    $pdf->Image($img, '', '', $imageWidth, 0, 'jpg', '', 'N', true, 300, '', false, false, array('LTRB' => array('width' => $imagesPadding, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255))));
                } else {
                    $pdf->Image($img, '', '', $imageWidth, 0, 'jpg', '', 'T', true, 300, '', false, false, array('LTRB' => array('width' => $imagesPadding, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255))));
                }

            }
        }

        if ($path == '') {
            return $pdf->Output('temp.pdf', 'S');
        } else {
            $pdf->Output($path, 'F');
            $pdf->Close();
            return true;
        }
    }

    private function fixHtmlCharacters($page){



        return $page;
    }

    private function addReferringProviderData($encounter, $tokens, $allNeededInfo)
    {

        $info = $this->getReferringProviderData($encounter);

        if (empty($info)) {
            return $allNeededInfo;
        }

        unset($provider);
        foreach ($tokens as $i => $tok) {
            if (isset($info[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $info[$tok];
            }
        }

        return $allNeededInfo;
    }

    private function getReferringProviderData($encounter)
    {

        if (!isset($encounter['referring_physician']) || $encounter['referring_physician'] == 0) {
            return [];
        }

        $provider = $this->ReferringProviders->getReferringProviderById($this->encounter['referring_physician']);

        if ($provider === false) {
            return [];
        }

        $data = [
            '[REFERRING_ID]' => $provider['id'],
            '[REFERRING_TITLE]' => isset($provider['title']) ? $provider['title'] : '',
            '[REFERRING_FULL_NAME]' => Person::fullname($provider['fname'], $provider['mname'], $provider['lname']),
            '[REFERRING_FIRST_NAME]' => isset($provider['fname']) ? $provider['fname'] : '',
            '[REFERRING_MIDDLE_NAME]' => isset($provider['mname']) ? $provider['mname'] : '',
            '[REFERRING_LAST_NAME]' => isset($provider['lname']) ? $provider['lname'] : '',
            '[REFERRING_NPI]' => isset($provider['npi']) ? $provider['npi'] : '',
            '[REFERRING_LIC]' => isset($provider['lic']) ? $provider['lic'] : '',
            '[REFERRING_FED_TAX]' => isset($provider['ssn']) ? $provider['ssn'] : '',
            '[REFERRING_TAXONOMY]' => isset($provider['taxonomy']) ? $provider['taxonomy'] : '',
            '[REFERRING_EMAIL]' => isset($provider['email']) ? $provider['email'] : '',
            '[REFERRING_DIRECT_ADDRESS]' => isset($provider['direct_address']) ? $provider['direct_address'] : '',
            '[REFERRING_PHONE]' => isset($provider['phone_number']) ? $provider['phone_number'] : '',
            '[REFERRING_MOBILE]' => isset($provider['cel_number']) ? $provider['cel_number'] : '',
            '[REFERRING_FAX]' => isset($provider['fax_number']) ? $provider['fax_number'] : '',
        ];

        return $data;
    }


    private function addProviderData($params, $tokens, $allNeededInfo)
    {

        $info = $this->getProviderData($params);

        if (empty($info)) {
            return $allNeededInfo;
        }

        unset($provider);
        foreach ($tokens as $i => $tok) {
            if (isset($info[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $info[$tok];
            }
        }

        return $allNeededInfo;
    }

    private function getProviderData($params)
    {

        if (!isset($params->provider_uid)) return [];

        $provider = $this->User->getUserByUid($params->provider_uid);

        if ($provider === false) {
            return [];
        }

        $data = [
            '[PROVIDER_ID]' => $provider['id'],
            '[PROVIDER_TITLE]' => isset($provider['title']) ? $provider['title'] : '',
            '[PROVIDER_FULL_NAME]' => Person::fullname($provider['fname'], $provider['mname'], $provider['lname']),
            '[PROVIDER_FIRST_NAME]' => isset($provider['fname']) ? $provider['fname'] : '',
            '[PROVIDER_MIDDLE_NAME]' => isset($provider['mname']) ? $provider['mname'] : '',
            '[PROVIDER_LAST_NAME]' => isset($provider['lname']) ? $provider['lname'] : '',
            '[PROVIDER_SIGNATURE]' => isset($provider['signature']) ? $provider['signature'] : '',
            '[PROVIDER_NPI]' => isset($provider['npi']) ? $provider['npi'] : '',
            '[PROVIDER_LIC]' => isset($provider['lic']) ? $provider['lic'] : '',
            '[PROVIDER_DEA]' => isset($provider['feddrugid']) ? $provider['feddrugid'] : '',
            '[PROVIDER_SDI]' => isset($provider['statedrugid']) ? $provider['statedrugid'] : '',
            '[PROVIDER_FED_TAX]' => isset($provider['fedtaxid']) ? $provider['fedtaxid'] : '',
            '[PROVIDER_ESS]' => isset($provider['ess']) ? $provider['ess'] : '',
            '[PROVIDER_TAXONOMY]' => isset($provider['taxonomy']) ? $provider['taxonomy'] : '',
            '[PROVIDER_EMAIL]' => isset($provider['email']) ? $provider['email'] : '',
            '[PROVIDER_DIRECT_ADDRESS]' => isset($provider['direct_address']) ? $provider['direct_address'] : '',
            '[PROVIDER_ADDRESS_LINE_ONE]' => isset($provider['street']) ? $provider['street'] : '',
            '[PROVIDER_ADDRESS_LINE_TWO]' => isset($provider['street_cont']) ? $provider['street_cont'] : '',
            '[PROVIDER_ADDRESS_CITY]' => isset($provider['city']) ? $provider['city'] : '',
            '[PROVIDER_ADDRESS_STATE]' => isset($provider['state']) ? $provider['state'] : '',
            '[PROVIDER_ADDRESS_ZIP]' => isset($provider['postal_code']) ? $provider['postal_code'] : '',
            '[PROVIDER_ADDRESS_COUNTRY]' => isset($provider['country_code']) ? $provider['country_code'] : '',
            '[PROVIDER_PHONE]' => isset($provider['phone']) ? $provider['phone'] : '',
            '[PROVIDER_MOBILE]' => isset($provider['mobile']) ? $provider['mobile'] : '',
        ];

        return $data;
    }

    public function getPatientTokesDataByPid($pid)
    {
        $patientData = $this->getAllPatientData($pid);
        $age = $this->Patient->getPatientAgeByDOB($patientData['DOB']);

        $data = [
            '[PATIENT_NAME]' => $patientData['fname'],
            '[PATIENT_ID]' => $patientData['pid'],
            '[PATIENT_RECORD_NUMBER]' => $patientData['pubpid'],
            '[PATIENT_FULL_NAME]' => $this->Patient->getPatientFullNameByPid($patientData['pid']),
            '[PATIENT_LAST_NAME]' => $patientData['lname'],
            '[PATIENT_SEX]' => $patientData['sex'],
            '[PATIENT_BIRTHDATE]' => $this->dateToString($patientData['DOB']),
            '[PATIENT_MARITAL_STATUS]' => $patientData['marital_status'],
            '[PATIENT_SOCIAL_SECURITY]' => $patientData['SS'],
            '[PATIENT_EXTERNAL_ID]' => $patientData['pubpid'],
            '[PATIENT_DRIVERS_LICENSE]' => $patientData['drivers_license'],

            '[PATIENT_POSTAL_ADDRESS_LINE_ONE]' => isset($patientData['postal_address']) ? $patientData['postal_address'] : '',
            '[PATIENT_POSTAL_ADDRESS_LINE_TWO]' => isset($patientData['postal_address_cont']) ? $patientData['postal_address_cont'] : '',
            '[PATIENT_POSTAL_CITY]' => isset($patientData['postal_city']) ? $patientData['postal_city'] : '',
            '[PATIENT_POSTAL_STATE]' => isset($patientData['postal_state']) ? $patientData['postal_state'] : '',
            '[PATIENT_POSTAL_ZIP]' => isset($patientData['postal_zip']) ? $patientData['postal_zip'] : '',
            '[PATIENT_POSTAL_COUNTRY]' => isset($patientData['postal_country']) ? $patientData['postal_country'] : '',

            '[PATIENT_PHYSICAL_ADDRESS_LINE_ONE]' => isset($patientData['physical_address']) ? $patientData['physical_address'] : '',
            '[PATIENT_PHYSICAL_ADDRESS_LINE_TWO]' => isset($patientData['physical_address_cont']) ? $patientData['physical_address_cont'] : '',
            '[PATIENT_PHYSICAL_CITY]' => isset($patientData['physical_city']) ? $patientData['physical_city'] : '',
            '[PATIENT_PHYSICAL_STATE]' => isset($patientData['physical_state']) ? $patientData['physical_state'] : '',
            '[PATIENT_PHYSICAL_ZIP]' => isset($patientData['physical_zip']) ? $patientData['physical_zip'] : '',
            '[PATIENT_PHYSICAL_COUNTRY]' => isset($patientData['physical_country']) ? $patientData['physical_country'] : '',

            '[PATIENT_HOME_PHONE]' => isset($patientData['phone_home']) ? $patientData['phone_home'] : '',
            '[PATIENT_MOBILE_PHONE]' => isset($patientData['phone_mobile']) ? $patientData['phone_mobile'] : '',
            '[PATIENT_WORK_PHONE]' => isset($patientData['phone_work']) ? $patientData['phone_work'] : '',

            '[PATIENT_EMAIL]' => isset($patientData['email']) ? $patientData['email'] : '',

            '[PATIENT_MOTHERS_NAME]' => isset($patientData['mother_fname']) ?
                Person::fullname(
                    $patientData['mother_fname'],
                    $patientData['mother_mname'],
                    $patientData['mother_lname']
                ) : '',

            '[PATIENT_GUARDIANS_NAME]' => isset($patientData['guardians_fname']) ?
                Person::fullname(
                    $patientData['guardians_fname'],
                    $patientData['guardians_mname'],
                    $patientData['guardians_lname']
                ) : '',

            '[PATIENT_EMERGENCY_CONTACT]' => isset($patientData['emergency_contact_fname']) ?
                Person::fullname(
                    $patientData['emergency_contact_fname'],
                    $patientData['emergency_contact_mname'],
                    $patientData['emergency_contact_lname']
                ) : '',

            // TODO: Create a method to parse a phone number in the person dataProvider
            '[PATIENT_EMERGENCY_PHONE]' => isset($patientData['emergency_contact_phone']) ? $patientData['emergency_contact_phone'] : '',

            '[PATIENT_PROVIDER]' => is_numeric($patientData['provider']) ?
                $this->User->getUserFullNameById($patientData['provider']) : '',

            '[PATIENT_PHARMACY]' => $patientData['pharmacy'],
            '[PATIENT_AGE]' => $age['DMY']['years'],
            '[PATIENT_OCCUPATION]' => $patientData['occupation'],

            '[PATIENT_EMPLOYEER]' => isset($patientData['employer_name']) ? $patientData['employer_name'] : '',

            '[PATIENT_RACE]' => $patientData['race'],
            '[PATIENT_ETHNICITY]' => $patientData['ethnicity'],
            // TODO to be remove...
            '[PATIENT_LENGUAGE]' => $patientData['language'],
            '[PATIENT_LANGUAGE]' => $patientData['language'],
            '[PATIENT_PICTURE]' => '<img src="' . $patientData['image'] . '" style="width:100px;height:100px">',
            '[PATIENT_QRCODE]' => '<img src="' . $patientData['qrcode'] . '" style="width:100px;height:100px">',
        ];

        return $data;

    }

    public function get_PatientTokensData($pid, $allNeededInfo, $tokens)
    {
        $data = $this->getPatientTokesDataByPid($pid);

        foreach ($tokens as $i => $tok) {
            if (isset($data[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $data[$tok];
            };
        }
        return $allNeededInfo;
    }

    public function getDisclosureTokensData($disclosure, $allNeededInfo, $tokens)
    {
        $patient_language = $this->getPatientTokesDataByPid($disclosure->pid)['[PATIENT_LENGUAGE]'];

        if (!isset($patient_language)) $patient_language = 'es';

        include_once(ROOT . '/classes/Utils.php');

        $data = [
            '[DISCLOSURE_DOCUMENTS]' => $disclosure->document_inventory,
            '[DISCLOSURE_RECIPIENT]' => $disclosure->recipient,
            '[DISCLOSURE_DESCRIPTION]' => $disclosure->description,
            '[DISCLOSURE_REQUEST_DATE]' => Utils::dateTimeToString($disclosure->request_date, $patient_language),
            '[DISCLOSURE_FULFIL_DATE]' => Utils::dateTimeToString($disclosure->fulfil_date, $patient_language),
            '[DISCLOSURE_PICKUP_DATE]' => Utils::dateTimeToString($disclosure->pickup_date, $patient_language),
            '[DISCLOSURE_DOCUMENT_COUNT]' => (string)$disclosure->document_inventory_count
        ];

        foreach ($tokens as $i => $tok) {
            if (isset($data[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $data[$tok];
            };
        }
        return $allNeededInfo;
    }

    public function getFormatTokensData($pid, $allNeededInfo, $tokens)
    {
        $patient_language = $this->getPatientTokesDataByPid($pid)['[PATIENT_LENGUAGE]'];

        if (!isset($patient_language)) $patient_language = 'es';

        include_once(ROOT . '/classes/Utils.php');

        $data = [
            '[TODAY]' => Utils::dateTimeToString(date('Y-m-d H:i:s'), $patient_language),
            '[B]' => '<b>',
            '[/B]' => '</b>',
            '[U]' => '<u>',
            '[/U]' => '</u>',
            '[I]' => '<i>',
            '[/I]' => '</i>',
            '[TAB]' => '&nbsp;&nbsp;&nbsp;&nbsp;'
        ];

        foreach ($tokens as $i => $tok) {
            if (isset($data[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $data[$tok];
            };
        }
        return $allNeededInfo;
    }

    private function addReferralData($params, $tokens, $allNeededInfo)
    {

        $referral = new Referrals();
        $data = $referral->getPatientReferral($params->referralId);
        if ($data === false)
            return $allNeededInfo;

        $diagnosis = (isset($data['diagnosis_text']) && !empty($data['diagnosis_text']) ?
            $data['diagnosis_text'] . ' (' . $data['diagnosis_code_type'] . ':' . $data['diagnosis_code'] . ')' : '');
        $service = (isset($data['service_text']) && !empty($data['service_text']) ?
            $data['service_text'] . ' (' . $data['service_code_type'] . ':' . $data['service_code'] . ')' : '');

        $info = [
            '[REFERRAL_ID]' => $data['id'],
            '[REFERRAL_DATE]' => $data['referral_date'],
            '[REFERRAL_REASON]' => $data['referal_reason'],
            '[REFERRAL_DIAGNOSIS]' => $diagnosis,
            '[REFERRAL_SERVICE]' => $service,
            '[REFERRAL_RISK_LEVEL]' => $data['risk_level'],
            '[REFERRAL_BY_TEXT]' => $data['refer_by_text'],
            '[REFERRAL_TO_TEXT]' => $data['refer_to_text']
        ];

        unset($referral);
        foreach ($tokens as $i => $tok) {
            if (isset($info[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $info[$tok];
            }
        }
        return $allNeededInfo;
    }

    private function addDoctorsNoteData($params, $tokens, $allNeededInfo)
    {

        $DoctorsNotes = new DoctorsNotes();
        $data = $DoctorsNotes->getDoctorsNote($params->docNoteid);
        if ($data === false)
            return $allNeededInfo;
        $info = [
            '[DOC_NOTE_ID]' => $data['id'],
            '[DOC_NOTE_CREATE_DATE]' => $data['create_date'],
            '[DOC_NOTE_ORDER_DATE]' => $data['order_date'],
            '[DOC_NOTE_FROM_DATE]' => $data['from_date'],
            '[DOC_NOTE_TO_DATE]' => $data['to_date'],
//			'[DOC_NOTE_RETURN_DATE]' => $data['return_date'],
            '[DOC_NOTE_RESTRICTIONS]' => $this->arrayToOrderedList($data['restrictions']),
            '[DOC_NOTE_COMMENTS]' => $data['comments']
        ];
        unset($referral);
        foreach ($tokens as $i => $tok) {
            if (isset($info[$tok]) && ($allNeededInfo[$i] == '' || $allNeededInfo[$i] == null)) {
                $allNeededInfo[$i] = $info[$tok];
            }
        }
        return $allNeededInfo;
    }

    private function parseTokensForOrders($params, $tokens, $allNeededInfo)
    {

        if (isset($params->date_ordered)) {
            $index = array_search('[ORDER_DATE]', $tokens);
            if ($index !== false) {
                $allNeededInfo[$index] = $this->dateToString($params->date_ordered);
            }
        }

        if (isset($params->orderItems)) {
            $index = array_search('[ORDER_ITEMS]', $tokens);
            if ($index !== false) {
                $html = $this->arrayToTable($params->orderItems);
                $allNeededInfo[$index] = $html;
            }
        }

        return $allNeededInfo;
    }

    public function arrayToOrderedList($array)
    {
        if (!is_array($array) || count($array) == 0)
            return 'N/A';
        $ol = '<ol style="margin: 0">';
        foreach ($array as $list) {
            $ol .= '<li>' . $list . '</li>';
        }
        $ol .= '</ol>';
        return $ol;
    }

    public function arrayToUnorderedList($array)
    {
        if (!is_array($array) || count($array) == 0)
            return 'N/A';
        $ol = '<ul>';
        foreach ($array as $list) {
            $ol .= '<li>' . $list . '</li>';
        }
        $ol .= '</ul>';
        return $ol;
    }

    public function arrayToTable($array)
    {
        if (!is_array($array) || count($array) == 0)
            return 'N/A';

        $html = '';
        $max_rows = $this->max_order_rows;
        $running_rows = 0;


        // get header row
        $th = array_shift($array);

        $total_rows = count($array);

        // table rows
        foreach ($array as $index => $row) {

            // start table
            if ($running_rows === 0) {
                $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin: 0">';
                if (count($th) > 1 && $th[0] !== '') {
                    $html .= '<tr>';
                    foreach ($th as $cell) {
                        $html .= '<th style="border-bottom:1px solid #000000;">' . $cell . '</th>';
                    }
                    $html .= '</tr>';
                }
            }

            $html .= '<tr>';
            foreach ($row as $cell) {
                $color = ($index % 2 == 0 ? '#ffffff' : '#f6f6f6');
                $html .= '<td style="background-color:' . $color . ';">' . $cell . '</td>';
            }
            $html .= '</tr>';

            $running_rows++;

            // end table
            if ($total_rows === ($index + 1)) {
                $html .= '</table>';
            } else if ($running_rows === $max_rows) {
                $html .= '</table>';
                $html .= '{newpage}';
                $running_rows = 0;
            }

        }

        return $html;
    }

    private function isHtml($string)
    {
        return preg_match("/<[^<]+>/", $string, $m) != 0;
    }

    private function dateToString($date)
    {
        if (!isset($date)) return 'UNK';
        return date('F j, Y', strtotime($date));
    }

    private function getPdfTemplateByFacilityId($facility_id = null, $pdf_format = null)
    {

        $filters = [];

        if (isset($facility_id)) {
            $filters['facility_id'] = $facility_id;
        }
        if (isset($pdf_format)) {
            $filters['format'] = $pdf_format;
        }

        if (!empty($filters)) {


//message = "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'documents_pdf_templates.is_interface_tpl' in 'where clause'"


            $filters['active'] = 1;
			$filters['is_interface_tpl'] = 0;
            $record = $this->t->load($filters)->one();

            if ($record !== false) {
                $record['template'] = ROOT . $record['template'];
                return $record;
            }
        }

        return [
            'template' => ROOT . '/resources/templates/default.pdf',
            'body_margin_left' => 25,
            'body_margin_top' => 25,
            'body_margin_right' => 25,
            'body_font_family' => 'times',
            'body_font_style' => '',
            'body_font_size' => 12,
            'header_data' => null,
            'format' => isset($pdf_format) ? $pdf_format : 'LETTER'
        ];
    }

    public function getInterfacePdfTemplateByFacilityId($facility_id = null)
    {

        $filters = [];
        $filters['facility_id'] = $facility_id;
        $filters['is_interface_tpl'] = 1;
        $filters['active'] = 1;
        $record = $this->t->load($filters)->one();

        if ($record !== false) {
            $record['template'] = ROOT . $record['template'];
            return $record;
        }

        return $record;
    }


    /**
     * FACILITY DATA
     * @param $facility_id
     * @return array
     */

    public function getFacilityTokensDataByFacilityId($facility_id)
    {
        $facilityData = $this->getAllFacilityData($facility_id);

        $data = [
            '[FACILITY_ID]' => isset($facilityData['id']) ? $facilityData['id'] : '',
            '[FACILITY_CODE]' => isset($facilityData['code']) ? $facilityData['code'] : '',
            '[FACILITY_NAME]' => isset($facilityData['name']) ? $facilityData['name'] : '',

            '[FACILITY_LEGAL_NAME]' => isset($facilityData['legal_name']) ? $facilityData['legal_name'] : '',

            '[FACILITY_LNAME]' => isset($facilityData['lname']) ? $facilityData['lname'] : '',
            '[FACILITY_MNAME]' => isset($facilityData['mname']) ? $facilityData['mname'] : '',
            '[FACILITY_FNAME]' => isset($facilityData['fname']) ? $facilityData['fname'] : '',

            '[FACILITY_ENTITY]' => isset($facilityData['facility_entity']) ? $facilityData['facility_entity'] : '',
            '[FACILITY_ATTN]' => isset($facilityData['attn']) ? $facilityData['attn'] : '',
            '[FACILITY_REGION]' => isset($facilityData['region']) ? $facilityData['region'] : '',

            '[FACILITY_PHONE]' => isset($facilityData['phone']) ? $facilityData['phone'] : '',
            '[FACILITY_FAX]' => isset($facilityData['fax']) ? $facilityData['fax'] : '',
            '[FACILITY_EMAIL]' => isset($facilityData['email']) ? $facilityData['email'] : '',

            '[FACILITY_ADDRESS]' => isset($facilityData['address']) ? $facilityData['address'] : '',
            '[FACILITY_ADDRESS_CONT]' => isset($facilityData['address_cont']) ? $facilityData['address_cont'] : '',
            '[FACILITY_CITY]' => isset($facilityData['city']) ? $facilityData['city'] : '',
            '[FACILITY_STATE]' => isset($facilityData['state']) ? $facilityData['state'] : '',
            '[FACILITY_POSTAL_CODE]' => isset($facilityData['postal_code']) ? $facilityData['postal_code'] : '',
            '[FACILITY_COUNTRY_CODE]' => isset($facilityData['country_code']) ? $facilityData['country_code'] : '',

            '[FACILITY_POSTAL_ADDRESS]' => isset($facilityData['postal_address']) ? $facilityData['postal_address'] : '',
            '[FACILITY_POSTAL_ADDRESS_CONT]' => isset($facilityData['postal_address_cont']) ? $facilityData['postal_address_cont'] : '',
            '[FACILITY_POSTAL_CITY]' => isset($facilityData['postal_city']) ? $facilityData['postal_city'] : '',
            '[FACILITY_POSTAL_STATE]' => isset($facilityData['postal_state']) ? $facilityData['postal_state'] : '',
            '[FACILITY_POSTAL_ZIP_CODE]' => isset($facilityData['postal_zip_code']) ? $facilityData['postal_zip_code'] : '',
            '[FACILITY_POSTAL_COUNTRY_CODE]' => isset($facilityData['postal_country_code']) ? $facilityData['postal_country_code'] : '',

            '[FACILITY_EIN]' => isset($facilityData['ein']) ? $facilityData['ein'] : '',
            '[FACILITY_CLIA]' => isset($facilityData['clia']) ? $facilityData['clia'] : '',
            '[FACILITY_FDA]' => isset($facilityData['fda']) ? $facilityData['fda'] : '',
            '[FACILITY_NPI]' => isset($facilityData['npi']) ? $facilityData['npi'] : '',
            '[FACILITY_ESS]' => isset($facilityData['ess']) ? $facilityData['ess'] : '',

        ];

        return $data;

    }

    public function getAllFacilityData($facility_id)
    {
        $this->db->setSQL("SELECT * FROM facility WHERE id = '$facility_id'");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }


    /**
     * DEPARTMENT DATA
     * @param $department_id
     * @return array
     */

    public function getDepartmentTokensDataByDepartmentId($department_id)
    {
        $departmentData = $this->getAllDepartmentData($department_id);

        $data = [
            '[DEPARTMENT_ID]' => isset($departmentData['id']) ? $departmentData['id'] : '',
            '[DEPARTMENT_CODE]' => isset($departmentData['code']) ? $departmentData['code'] : '',
            '[DEPARTMENT_TITLE]' => isset($departmentData['title']) ? $departmentData['title'] : '',
        ];

        return $data;

    }

    public function getAllDepartmentData($department_id)
    {
        $this->db->setSQL("SELECT * FROM departments WHERE id = '$department_id'");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }

    /**
     * SPECIALTY DATA
     * @param $specialty_id
     * @return array
     */

    public function getSpecialtyTokensDataBySpecialtyId($specialty_id)
    {
        $specialtyData = $this->getAllSpecialtyData($specialty_id);

        $data = [
            '[SPECIALTY_ID]' => isset($specialtyData['id']) ? $specialtyData['id'] : '',
            '[SPECIALTY_CODE]' => isset($specialtyData['code']) ? $specialtyData['code'] : '',
            '[SPECIALTY_TITLE]' => isset($specialtyData['title']) ? $specialtyData['title'] : '',
            '[SPECIALTY_TAXONOMY]' => isset($specialtyData['taxonomy']) ? $specialtyData['taxonomy'] : '',
            '[SPECIALTY_MODALITY]' => isset($specialtyData['modality']) ? $specialtyData['modality'] : ''
        ];

        return $data;
    }

    public function getAllSpecialtyData($specialty_id)
    {
        $this->db->setSQL("SELECT * FROM specialties WHERE id = '$specialty_id'");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }


    /**
     * REFERRING DATA
     * @param $facility_id
     * @return array
     */

    public function getReferringProviderTokensDataByReferringId($referring_id)
    {
        $referringData = $this->getAllReferringProviderData($referring_id);

        $fname = isset($referringData['fname']) ? $referringData['fname'] : '';
        $mname = isset($referringData['mname']) ? $referringData['mname'] : '';
        $lname = isset($referringData['lname']) ? $referringData['lname'] : '';

        $data = [
            '[REFERRING_ID]' => $referringData['id'],
            '[REFERRING_CODE]' => isset($referringData['code']) ? $referringData['code'] : '',
            '[REFERRING_USERNAME]' => isset($referringData['username']) ? $referringData['username'] : '',
            '[REFERRING_TITLE]' => isset($referringData['title']) ? $referringData['title'] : '',

            '[REFERRING_FULL_NAME]' => Person::fullname($fname, $mname, $lname),

            '[REFERRING_FIRST_NAME]' => $fname,
            '[REFERRING_MIDDLE_NAME]' => $mname,
            '[REFERRING_LAST_NAME]' => $lname,

            '[REFERRING_NPI]' => isset($referringData['npi']) ? $referringData['npi'] : '',
            '[REFERRING_LIC]' => isset($referringData['lic']) ? $referringData['lic'] : '',
            '[REFERRING_FED_TAX]' => isset($referringData['ssn']) ? $referringData['ssn'] : '',
            '[REFERRING_TAXONOMY]' => isset($referringData['taxonomy']) ? $referringData['taxonomy'] : '',
            '[REFERRING_EMAIL]' => isset($referringData['email']) ? $referringData['email'] : '',
            '[REFERRING_DIRECT_ADDRESS]' => isset($referringData['direct_address']) ? $referringData['direct_address'] : '',
            '[REFERRING_PHONE]' => isset($referringData['phone_number']) ? $referringData['phone_number'] : '',
            '[REFERRING_MOBILE]' => isset($referringData['cel_number']) ? $referringData['cel_number'] : '',
            '[REFERRING_FAX]' => isset($referringData['fax_number']) ? $referringData['fax_number'] : '',
        ];

        return $data;

    }

    public function getAllReferringProviderData($referring_id)
    {
        $this->db->setSQL("SELECT * FROM referring_providers WHERE id = '$referring_id'");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }


    /**
     * INSURANCE DATA
     * @param $department_id
     * @return array
     */

    public function getInsuranceCompanyTokensDataByInsuranceId($insurance_id)
    {
        $insuranceData = $this->getAllInsuranceCompanyData($insurance_id);

        $data = [
            '[INSURANCE_ID]' => isset($insuranceData['id']) ? $insuranceData['id'] : '',
            '[INSURANCE_CODE]' => isset($insuranceData['code']) ? $insuranceData['code'] : '',
            '[INSURANCE_TITLE]' => isset($insuranceData['name']) ? $insuranceData['name'] : '',

            '[INSURANCE_ADDRESS1]' => isset($insuranceData['address1']) ? $insuranceData['address1'] : '',
            '[INSURANCE_ADDRESS2]' => isset($insuranceData['address2']) ? $insuranceData['address2'] : '',
            '[INSURANCE_CITY]' => isset($insuranceData['city']) ? $insuranceData['city'] : '',
            '[INSURANCE_STATE]' => isset($insuranceData['state']) ? $insuranceData['state'] : '',

            '[INSURANCE_PHONE1]' => isset($insuranceData['phone1']) ? $insuranceData['phone1'] : '',
            '[INSURANCE_PHONE2]' => isset($insuranceData['phone2']) ? $insuranceData['phone2'] : '',
            '[INSURANCE_FAX]' => isset($insuranceData['fax']) ? $insuranceData['fax'] : '',
        ];

        return $data;

    }

    public function getAllInsuranceCompanyData($insurance_id)
    {
        $this->db->setSQL("SELECT * FROM insurance_companies WHERE id = '$insurance_id'");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }


    /**
     * PATIENT INSURANCE DATA
     * @param $patient_insurance_id
     * @return array
     */

    public function getPatientInsuranceTokensDataByPatientInsuranceId($patient_insurance_id)
    {
        $patientInsuranceData = $this->getAllPatientInsuranceData($patient_insurance_id);

        $data = [

            '[INSURANCE_COMPANY_ID]' => isset($patientInsuranceData['insurance_company_id']) ? $patientInsuranceData['insurance_company_id'] : '',
            '[INSURANCE_COMPANY_NAME]' => isset($patientInsuranceData['insurance_company_name']) ? $patientInsuranceData['insurance_company_name'] : '',
            '[INSURANCE_TYPE]' => isset($patientInsuranceData['insurance_type']) ? $patientInsuranceData['insurance_type'] : '',
            '[EFFECTIVE_DATE]' => isset($patientInsuranceData['effective_date']) ? $this->dateToString($patientInsuranceData['effective_date']) : '',
            '[GROUP_NUMBER]' => isset($patientInsuranceData['group_number']) ? $patientInsuranceData['group_number'] : '',
            '[POLICY_NUMBER]' => isset($patientInsuranceData['policy_number']) ? $patientInsuranceData['policy_number'] : '',
            '[COVER_DESCRIPTION]' => isset($patientInsuranceData['cover_description']) ? $patientInsuranceData['cover_description'] : '',
            '[CARD_FIRST_NAME]' => isset($patientInsuranceData['card_first_name']) ? $patientInsuranceData['card_first_name'] : '',
            '[CARD_MIDDLE_NAME]' => isset($patientInsuranceData['card_middle_name']) ? $patientInsuranceData['card_middle_name'] : '',
            '[CARD_LAST_NAME]' => isset($patientInsuranceData['card_last_name']) ? $patientInsuranceData['card_last_name'] : '',

            '[SUBSCRIBER_POLICY_NUMBER]' => isset($patientInsuranceData['subscriber_policy_number']) ? $patientInsuranceData['subscriber_policy_number'] : '',
            '[SUBSCRIBER_TITLE]' => isset($patientInsuranceData['subscriber_title']) ? $patientInsuranceData['subscriber_title'] : '',
            '[SUBSCRIBER_GIVEN_NAME]' => isset($patientInsuranceData['subscriber_given_name']) ? $patientInsuranceData['subscriber_given_name'] : '',
            '[SUBSCRIBER_MIDDLE_NAME]' => isset($patientInsuranceData['subscriber_middle_name']) ? $patientInsuranceData['subscriber_middle_name'] : '',
            '[SUBSCRIBER_SURNAME]' => isset($patientInsuranceData['subscriber_surname']) ? $patientInsuranceData['subscriber_surname'] : '',

            '[SUBSCRIBER_RELATIONSHIP]' => isset($patientInsuranceData['subscriber_relationship']) ? $patientInsuranceData['subscriber_relationship'] : '',
            '[SUBSCRIBER_SEX]' => isset($patientInsuranceData['subscriber_sex']) ? $patientInsuranceData['subscriber_sex'] : '',
            '[SUBSCRIBER_DOB]' => isset($patientInsuranceData['subscriber_dob']) ? $this->dateToString($patientInsuranceData['subscriber_dob']) : '',
            '[SUBSCRIBER_SS]' => isset($patientInsuranceData['subscriber_ss']) ? $patientInsuranceData['subscriber_ss'] : '',

            '[SUBSCRIBER_STREET]' => isset($patientInsuranceData['subscriber_street']) ? $patientInsuranceData['subscriber_street'] : '',
            '[SUBSCRIBER_STREET_CONT]' => isset($patientInsuranceData['subscriber_street_cont']) ? $patientInsuranceData['subscriber_street_cont'] : '',
            '[SUBSCRIBER_CITY]' => isset($patientInsuranceData['subscriber_cty']) ? $patientInsuranceData['subscriber_cty'] : '',
            '[SUBSCRIBER_STATE]' => isset($patientInsuranceData['subscriber_state']) ? $patientInsuranceData['subscriber_state'] : '',
            '[SUBSCRIBER_COUNTRY]' => isset($patientInsuranceData['subscriber_country']) ? $patientInsuranceData['subscriber_country'] : '',
            '[SUBSCRIBER_POSTAL_CODE]' => isset($patientInsuranceData['subscriber_postal_code']) ? $patientInsuranceData['subscriber_postal_code'] : '',
            '[SUBSCRIBER_PHONE]' => isset($patientInsuranceData['subscriber_phone']) ? $patientInsuranceData['subscriber_phone'] : '',
            '[SUBSCRIBER_EMPLOYER]' => isset($patientInsuranceData['subscriber_employer']) ? $patientInsuranceData['subscriber_employer'] : '',
            '[SUBSCRIBER_NOTES]' => isset($patientInsuranceData['notes']) ? $patientInsuranceData['notes'] : '',
            '[SUBSCRIBER_ACTIVE]' => isset($patientInsuranceData['active']) ? $patientInsuranceData['active'] : '',


//            '[SUBSCRIBER_IMAGE]' => '<img src="' . $patientInsuranceData['image'] . '" style="width:100px;height:100px">',


        ];

        return $data;

    }


    /**
     * PATIENT OTHER INSURANCE DATA
     * @param $patient_other_insurance_id
     * @return array
     */

    public function getPatientOtherInsuranceTokensDataByPatientOtherInsuranceId($patient_other_insurance_id)
    {
        $patientOtherInsuranceData = $this->getAllPatientInsuranceData($patient_other_insurance_id);

        $data = [

            '[OTHER_INSURANCE_COMPANY_ID]' => isset($patientOtherInsuranceData['insurance_company_id']) ? $patientOtherInsuranceData['insurance_company_id'] : '',
            '[OTHER_INSURANCE_COMPANY_NAME]' => isset($patientOtherInsuranceData['insurance_company_name']) ? $patientOtherInsuranceData['insurance_company_name'] : '',
            '[OTHER_INSURANCE_TYPE]' => isset($patientOtherInsuranceData['insurance_type']) ? $patientOtherInsuranceData['insurance_type'] : '',
            '[OTHER_EFFECTIVE_DATE]' => isset($patientOtherInsuranceData['effective_date']) ? $this->dateToString($patientOtherInsuranceData['effective_date']) : '',
            '[OTHER_GROUP_NUMBER]' => isset($patientOtherInsuranceData['group_number']) ? $patientOtherInsuranceData['group_number'] : '',
            '[OTHER_POLICY_NUMBER]' => isset($patientOtherInsuranceData['policy_number']) ? $patientOtherInsuranceData['policy_number'] : '',
            '[OTHER_COVER_DESCRIPTION]' => isset($patientOtherInsuranceData['cover_description']) ? $patientOtherInsuranceData['cover_description'] : '',
            '[OTHER_CARD_FIRST_NAME]' => isset($patientOtherInsuranceData['card_first_name']) ? $patientOtherInsuranceData['card_first_name'] : '',
            '[OTHER_CARD_MIDDLE_NAME]' => isset($patientOtherInsuranceData['card_middle_name']) ? $patientOtherInsuranceData['card_middle_name'] : '',
            '[OTHER_CARD_LAST_NAME]' => isset($patientOtherInsuranceData['card_last_name']) ? $patientOtherInsuranceData['card_last_name'] : '',

            '[OTHER_SUBSCRIBER_POLICY_NUMBER]' => isset($patientOtherInsuranceData['subscriber_policy_number']) ? $patientOtherInsuranceData['subscriber_policy_number'] : '',
            '[OTHER_SUBSCRIBER_TITLE]' => isset($patientOtherInsuranceData['subscriber_title']) ? $patientOtherInsuranceData['subscriber_title'] : '',
            '[OTHER_SUBSCRIBER_GIVEN_NAME]' => isset($patientOtherInsuranceData['subscriber_given_name']) ? $patientOtherInsuranceData['subscriber_given_name'] : '',
            '[OTHER_SUBSCRIBER_MIDDLE_NAME]' => isset($patientOtherInsuranceData['subscriber_middle_name']) ? $patientOtherInsuranceData['subscriber_middle_name'] : '',
            '[OTHER_SUBSCRIBER_SURNAME]' => isset($patientOtherInsuranceData['subscriber_surname']) ? $patientOtherInsuranceData['subscriber_surname'] : '',

            '[OTHER_SUBSCRIBER_RELATIONSHIP]' => isset($patientOtherInsuranceData['subscriber_relationship']) ? $patientOtherInsuranceData['subscriber_relationship'] : '',
            '[OTHER_SUBSCRIBER_SEX]' => isset($patientOtherInsuranceData['subscriber_sex']) ? $patientOtherInsuranceData['subscriber_sex'] : '',
            '[OTHER_SUBSCRIBER_DOB]' => isset($patientOtherInsuranceData['subscriber_dob']) ? $this->dateToString($patientOtherInsuranceData['subscriber_dob']) : '',
            '[OTHER_SUBSCRIBER_SS]' => isset($patientOtherInsuranceData['subscriber_ss']) ? $patientOtherInsuranceData['subscriber_ss'] : '',

            '[OTHER_SUBSCRIBER_STREET]' => isset($patientOtherInsuranceData['subscriber_street']) ? $patientOtherInsuranceData['subscriber_street'] : '',
            '[OTHER_SUBSCRIBER_STREET_CONT]' => isset($patientOtherInsuranceData['subscriber_street_cont']) ? $patientOtherInsuranceData['subscriber_street_cont'] : '',
            '[OTHER_SUBSCRIBER_CITY]' => isset($patientOtherInsuranceData['subscriber_cty']) ? $patientOtherInsuranceData['subscriber_cty'] : '',
            '[OTHER_SUBSCRIBER_STATE]' => isset($patientOtherInsuranceData['subscriber_state']) ? $patientOtherInsuranceData['subscriber_state'] : '',
            '[OTHER_SUBSCRIBER_COUNTRY]' => isset($patientOtherInsuranceData['subscriber_country']) ? $patientOtherInsuranceData['subscriber_country'] : '',
            '[OTHER_SUBSCRIBER_POSTAL_CODE]' => isset($patientOtherInsuranceData['subscriber_postal_code']) ? $patientOtherInsuranceData['subscriber_postal_code'] : '',
            '[OTHER_SUBSCRIBER_PHONE]' => isset($patientOtherInsuranceData['subscriber_phone']) ? $patientOtherInsuranceData['subscriber_phone'] : '',
            '[OTHER_SUBSCRIBER_EMPLOYER]' => isset($patientOtherInsuranceData['subscriber_employer']) ? $patientOtherInsuranceData['subscriber_employer'] : '',
            '[OTHER_SUBSCRIBER_NOTES]' => isset($patientOtherInsuranceData['notes']) ? $patientOtherInsuranceData['notes'] : '',
            '[OTHER_SUBSCRIBER_ACTIVE]' => isset($patientOtherInsuranceData['active']) ? $patientOtherInsuranceData['active'] : '',

            //            '[OTHER_SUBSCRIBER_IMAGE]' => '<img src="' . $patientOtherInsuranceData['image'] . '" style="width:100px;height:100px">',

        ];

        return $data;

    }

    public function getAllPatientInsuranceData($patient_insurance_id)
    {
        $this->db->setSQL("SELECT ic.code as insurance_company_code,
                                      ic.name as insurance_company_name,
                                      pi.insurance_type as insurance_type,
                                      pi.effective_date as effective_date, 
                                      pi.group_number as group_number,
                                      pi.policy_number as policy_number,
                                      pi.cover_description as cover_description,
                                      pi.card_first_name as card_first_name,
                                      pi.card_middle_name as card_middle_name,
                                      pi.card_last_name as card_last_name,
                                      pi.subscriber_policy_number as subscriber_policy_number,
                                      pi.subscriber_title as subscriber_title,
                                      pi.subscriber_given_name as subscriber_given_name,
                                      pi.subscriber_middle_name as subscriber_middle_name,
                                      pi.subscriber_surname as subscriber_surname,
                                      pi.subscriber_relationship as subscriber_relationship,
                                      pi.subscriber_sex as subscriber_sex,
                                      pi.subscriber_dob as subscriber_dob,
                                      pi.subscriber_ss as subscriber_ss,
                                      pi.subscriber_street as subscriber_street,
                                      pi.subscriber_street_cont as subscriber_street_cont,
                                      pi.subscriber_city as subscriber_city,
                                      pi.subscriber_state as subscriber_state,
                                      pi.subscriber_country as subscriber_country,
                                      pi.subscriber_postal_code as subscriber_postal_code,
                                      pi.subscriber_phone as subscriber_phone,
                                      pi.subscriber_employer as subscriber_employer,
                                      pi.notes as notes,
                                      pi.image as image,
                                      pi.active as active
                                      FROM patient_insurances pi left join insurance_companies ic on ic.id = pi.insurance_id
                                      WHERE pi.id = '$patient_insurance_id'");
        return $this->db->fetchRecord(PDO::FETCH_ASSOC);
    }



}