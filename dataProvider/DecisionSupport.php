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
include_once(ROOT . '/dataProvider/Vitals.php');
include_once(ROOT . '/dataProvider/Orders.php');
include_once(ROOT . '/dataProvider/Allergies.php');
include_once(ROOT . '/dataProvider/Medications.php');
include_once(ROOT . '/dataProvider/Procedures.php');
include_once(ROOT . '/dataProvider/SocialHistory.php');
include_once(ROOT . '/dataProvider/ActiveProblems.php');
include_once(ROOT . '/dataProvider/ACL.php');
include_once(ROOT . '/lib/Matcha/plugins/Carbon/Carbon.php');

class DecisionSupport
{
    /**
     * @var MatchaCUP Rules Model
     */
    private $r;
    /**
     * @var MatchaCUP Rule Items Model
     */
    private $rc;

    /**
     * @var Access Control List
     */
    private $ACL;

    /**
     * @var Patient
     */
    private $Patient;
    /**
     * @var Medications
     */
    private $Medications;
    /**
     * @var Procedures
     */
    private $Procedures;
    /**
     * @var ActiveProblems
     */
    private $ActiveProblems;
    /**
     * @var Orders
     */
    private $Orders;
    /**
     * @var Allergies
     */
    private $Allergies;
    /**
     * @var Vitals
     */
    private $Vitals;
    /**
     * @var SocialHistory
     */
    private $SocialHistory;
    /**
     * @var null | Array
     */
    private $rules = null;

    function __construct()
    {
        if ($this->r == NULL)
            $this->r = MatchaModel::setSenchaModel('App.model.administration.DecisionSupportRule');
        if ($this->rc == NULL)
            $this->rc = MatchaModel::setSenchaModel('App.model.administration.DecisionSupportRuleConcept');

        $this->ACL = new ACL($_SESSION['user']['id']);
        $this->Orders = new Orders();
        $this->Vitals = new Vitals();
        $this->Allergies = new Allergies();
        $this->Medications = new Medications();
        $this->Procedures = new Procedures();
        $this->SocialHistory = new SocialHistory();
        $this->ActiveProblems = new ActiveProblems();
    }

    public function getDecisionSupportRules($params)
    {
        $append = null;
        if(isset($params->filter)) $append = " WHERE active=".$params->filter[0]->value." AND alert_type='".$params->filter[1]->value."'";

        $sql = "SELECT support_rules.* 
        FROM support_rules".$append;

        $buildWhere = NULL;
        if(isset($params->filter)) {
            foreach ($params->filter as $filter) {
                if ($filter->property == 'category') {
                    if (!$buildWhere) $buildWhere = " AND (";
                    $buildWhere .= "category = '$filter->value' OR ";
                }
            }
        }
        if($buildWhere) $buildWhere = substr($buildWhere, 0, -4) . ")";
        $this->r->sql($sql . $buildWhere);
        $records['data'] = $this->r->all();

        foreach ($records['data'] as $key => $record) {
            switch ($record['category']) {
                case 'C':
                    $records['data'][$key]['category_name'] = 'Clinical';
                    break;
                case 'A':
                    $records['data'][$key]['category_name'] = 'Administrative';
                    break;
                case 'P':
                    $records['data'][$key]['category_name'] = 'Physician';
                    break;
                case 'N':
                    $records['data'][$key]['category_name'] = 'Nurse';
                    break;
            }
        }
        return $records;
    }

    public function getDecisionSupportRule($params)
    {
        return $this->r->load($params)->one();
    }

    public function addDecisionSupportRule($params)
    {
        return $this->r->save($params);
    }

    public function updateDecisionSupportRule($params)
    {
        return $this->r->save($params);
    }

    public function deleteDecisionSupportRule($params)
    {
        $filters = new stdClass();
        $filters->filter[0] = new stdClass();
        $filters->filter[0]->property = 'rule_id';
        // remove all rule concepts
        if (is_array($params)) {
            foreach ($params as $param) {
                $filters->filter[0]->value = $param->id;
                $this->deleteDecisionSupportRuleConcept($filters);
            }
        } else {
            $filters->filter[0]->value = $params->id;
            $this->deleteDecisionSupportRuleConcept($filters);
        }
        return $this->r->destroy($params);
    }

    public function getDecisionSupportRuleConcepts($params)
    {
        return $this->rc->load($params)->all();
    }

    public function getDecisionSupportRuleConcept($params)
    {
        return $this->rc->load($params)->one();
    }

    public function addDecisionSupportRuleConcept($params)
    {
        return $this->rc->save($params);
    }

    public function updateDecisionSupportRuleConcept($params)
    {
        return $this->rc->save($params);
    }

    public function deleteDecisionSupportRuleConcept($params)
    {
        return $this->rc->destroy($params);
    }

    /**
     * @param object $params $params->pid and $params->alertType required
     * @return array
     */
    public function getAlerts($params)
    {
        $this->Patient = new Patient($params->pid);
        $this->setRules($params->alertType);

        $alerts = [];

        foreach ($this->rules as $rule) {
            $alert = $this->ckRule($rule);
            if ($alert !== false) {
                unset($rule['concepts']); // remove concepts
                $alerts[] = $rule;
            }
        }
        return $alerts;
    }

    private function setRules($alertType)
    {
        $params = new stdClass();
        $fKey = 0;
        $params->filter[$fKey] = new stdClass();
        $params->filter[$fKey]->property = 'active';
        $params->filter[$fKey]->value = 1;

        $fKey++;
        $params->filter[$fKey] = new stdClass();
        $params->filter[$fKey]->property = 'alert_type';
        $params->filter[$fKey]->value = $alertType;

        if ($this->ACL->hasPermission('decision_support_administrator')) {
            $fKey++;
            $params->filter[$fKey] = new stdClass();
            $params->filter[$fKey]->property = 'category';
            $params->filter[$fKey]->value = 'A';
        }

        if ($this->ACL->hasPermission('decision_support_clinical')) {
            $fKey++;
            $params->filter[$fKey] = new stdClass();
            $params->filter[$fKey]->property = 'category';
            $params->filter[$fKey]->value = 'C';
        }

        if ($this->ACL->hasPermission('decision_support_physician')) {
            $fKey++;
            $params->filter[$fKey] = new stdClass();
            $params->filter[$fKey]->property = 'category';
            $params->filter[$fKey]->value = 'P';
        }

        if ($this->ACL->hasPermission('decision_support_nurse')) {
            $fKey++;
            $params->filter[$fKey] = new stdClass();
            $params->filter[$fKey]->property = 'category';
            $params->filter[$fKey]->value = 'N';
        }

        $this->rules = $this->getDecisionSupportRules($params);
        $this->rules = $this->rules['data'];

        // Unset all the categories from params
        foreach($params->filter as $key => $filter){
            if($filter->property == 'category') unset($params->filter[$key]);
        }

        // change property to filter concepts
        $params->filter[0]->property = 'rule_id';
        $params->filter[1]->property = 'concept_type';

        Matcha::pauseLog(true);

        foreach ($this->rules as $i => $rule) {
            $params->filter[0]->value = $rule['id'];

            $params->filter[1]->value = 'PROC';
            $this->rules[$i]['concepts']['PROC'] = $this->getDecisionSupportRuleConcepts($params);

            $params->filter[1]->value = 'PROB';
            $this->rules[$i]['concepts']['PROB'] = $this->getDecisionSupportRuleConcepts($params);

            $params->filter[1]->value = 'SOCI';
            $this->rules[$i]['concepts']['SOCI'] = $this->getDecisionSupportRuleConcepts($params);

            $params->filter[1]->value = 'MEDI';
            $this->rules[$i]['concepts']['MEDI'] = $this->getDecisionSupportRuleConcepts($params);

            $params->filter[1]->value = 'ALLE';
            $this->rules[$i]['concepts']['ALLE'] = $this->getDecisionSupportRuleConcepts($params);

            $params->filter[1]->value = 'LAB';
            $this->rules[$i]['concepts']['LAB'] = $this->getDecisionSupportRuleConcepts($params);

            $params->filter[1]->value = 'VITA';
            $this->rules[$i]['concepts']['VITA'] = $this->getDecisionSupportRuleConcepts($params);
        }
        // unset params since will not be use again
        unset($params);

        Matcha::pauseLog(false);
    }

    /**
     * @param $rule
     * @return bool Alert
     */
    private function ckRule($rule)
    {
        $alert = $this->ckDemographics($rule);

        if ($alert && isset($rule['concepts'])) {
            $procedureAlert = $this->ckProcedures($rule);
            $activeProblemsAlert = $this->ckActiveProblems($rule);
            $medicationsAlert = $this->ckActiveMedications($rule);
            $activeMedicationsAllergyAlert = $this->ckActiveMedicationAllergies($rule);
            $laboratoryAlert = $this->ckLaboratoryResults($rule);
            $socialHistoryAlert = $this->ckSocialHistory($rule);
            $vitalsAlert = $this->ckVitals($rule);

            return ($procedureAlert &&
                $activeProblemsAlert &&
                $medicationsAlert &&
                $activeMedicationsAllergyAlert &&
                $laboratoryAlert &&
                $socialHistoryAlert &&
                $vitalsAlert);

        } else {
            return $alert;
        }
    }

    /**
     * this method will always return true unless one of the conditions is met.
     * @param $rule
     * @return bool
     */
    private function ckDemographics($rule)
    {
        $age = $this->Patient->getPatientAge();
        // if age_start if not unlimited and patient age is less than age_start, return false
        if ($rule['age_start'] > 0 && $age['DMY']['years'] < $rule['age_start']) {
            return false;
        }
        // if age_end if not unlimited and patient age is grater than age_end, return false
        if ($rule['age_end'] > 0 && $age['DMY']['years'] > $rule['age_end']) {
            return false;
        }
        if ($rule['sex'] !== '' && $this->Patient->getPatientSex() != $rule['sex']) {
            return false;
        }
        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ckProcedures($rule)
    {
        if (isset($rule['concepts']['PROC']) && !empty($rule['concepts']['PROC'])) {
            $count = 0;
            foreach ($rule['concepts']['PROC'] as $concept) {
                $procedures = $this->Procedures->getPatientProceduresByPidAndCode(
                    $this->Patient->getPatientPid(),
                    $concept['concept_code']
                );
                if (empty($procedures)) continue;
                if ($concept['frequency_interval'] == 0) {
                    $count++;
                    continue;
                }
                $frequency = 0;
                foreach ($procedures as $procedure) {
                    if ($this->isWithInterval(
                        $procedure['create_date'],
                        $concept['frequency_interval'],
                        $concept['frequency_operator'],
                        'Y-m-d H:i:s')
                    ) {
                        $frequency++;
                        if ($concept['frequency'] == $frequency) break;
                    }
                }

                if ($concept['frequency_operator'] == '' ||
                    $this->compare($frequency, $concept['frequency_operator'], $concept['frequency'])
                ) {
                    $count++;
                }
            }
            return $count == count($rule['concepts']['PROC']);
        }
        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ckActiveProblems($rule)
    {
        if (isset($rule['concepts']['PROB']) && !empty($rule['concepts']['PROB'])) {
            $count = 0;
            foreach ($rule['concepts']['PROB'] as $concept) {
                $problems = $this->ActiveProblems->getPatientActiveProblemByPidAndCode(
                    $this->Patient->getPatientPid(),
                    $concept['concept_code']
                );
                if (empty($problems)) continue;
                if ($concept['frequency_interval'] == 0) {
                    $count++;
                    continue;
                }
                $frequency = 0;
                foreach ($problems as $problem) {
                    if ($this->isWithInterval(
                        $problem['begin_date'],
                        $concept['frequency_interval'],
                        $concept['frequency_operator'], 'Y-m-d')
                    ) {
                        $frequency++;
                        if ($concept['frequency'] == $frequency) break;
                    }
                }

                if ($concept['frequency_operator'] == '' || $this->compare($frequency, $concept['frequency_operator'], $concept['frequency'])) {
                    $count++;
                }
            }
            return $count == count($rule['concepts']['PROB']);
        }
        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ckActiveMedications($rule)
    {

        if (isset($rule['concepts']['MEDI']) && !empty($rule['concepts']['MEDI'])) {
            $count = 0;
            foreach ($rule['concepts']['MEDI'] as $concept) {
                $medications = $this->Medications->getPatientActiveMedicationsByPidAndCode(
                    $this->Patient->getPatientPid(),
                    $concept['concept_code']
                );
                if (empty($medications)) continue;
                $count++;
            }
            // meditations found should equal the medication concepts
            return $count == count($rule['concepts']['MEDI']);
        }

        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ckActiveMedicationAllergies($rule)
    {
        if (isset($rule['concepts']['ALLE']) && !empty($rule['concepts']['ALLE'])) {
            $count = 0;
            foreach ($rule['concepts']['ALLE'] as $concept) {
                $allergies = $this->Allergies->getPatientActiveDrugAllergiesByPidAndCode(
                    $this->Patient->getPatientPid(),
                    $concept['concept_code']
                );
                if (empty($allergies)) continue;
                if ($concept['frequency_interval'] == 0) {
                    $count++;
                    continue;
                }
                $frequency = 0;
                foreach ($allergies as $allergy) {
                    if ($this->isWithInterval($allergy['begin_date'], $concept['frequency_interval'], $concept['frequency_operator'], 'Y-m-d')) {
                        $frequency++;
                        if ($concept['frequency'] == $frequency) break;
                    }
                }
                if ($concept['frequency_operator'] == '' || $this->compare($frequency, $concept['frequency_operator'], $concept['frequency'])) {
                    $count++;
                }
            }
            return $count == count($rule['concepts']['ALLE']);
        }
        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ckLaboratoryResults($rule)
    {
    	// returns true is no alert found
        if (isset($rule['concepts']['LAB']) && !empty($rule['concepts']['LAB'])) {

            $alerts_found = 0;

            foreach ($rule['concepts']['LAB'] as $concept) {
                $observations = $this->Orders->getOrderResultObservationsByPidAndCode(
                    $this->Patient->getPatientPid(),
                    $concept['concept_code']
                );

                // how may found
                $frequency = 0;

                if (empty($observations) && $this->compare($frequency, $concept['frequency_operator'], $concept['frequency'])) {
                    $alerts_found++;
                    continue;
                };

                foreach ($observations as $observation) {

	                $isWithInterval = $concept['frequency_interval'] == '' ||
		                $this->isWithInterval($observation['result_date'], $concept['frequency_interval'], $concept['frequency_operator'], 'Y-m-d');

	                if($isWithInterval && ($concept['value'] == '' || $this->compare($observation['value'], $concept['value_operator'], $concept['value']))){
		                $frequency++;
	                }
                }

                if (
                	$concept['frequency_operator'] == '' ||
	                $this->compare($frequency, $concept['frequency_operator'], $concept['frequency'])) {
	                $alerts_found++;
                }
            }

            return $alerts_found == count($rule['concepts']['LAB']);
        }
        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ckSocialHistory($rule)
    {
        if (isset($rule['concepts']['SOCI']) && !empty($rule['concepts']['SOCI'])) {
            $count = 0;
            foreach ($rule['concepts']['SOCI'] as $concept) {
                $socials = $this->SocialHistory->getSocialHistoryByPidAndCode($this->Patient->getPatientPid(), $concept['concept_code']);
                if ($concept['frequency_interval'] == '') {
                    if ($this->compare(count($socials), $concept['frequency_operator'], $concept['frequency'])) {
                        return true;
                    }
                } else {
                    $frequency = 0;
                    foreach ($socials as $social) {
                        $starDate = isset($social['create_date']) ? $social['create_date'] : $social['start_date'];
                        if ($this->isWithInterval($starDate, $concept['frequency_interval'], $concept['frequency_operator'], 'Y-m-d H:i:s')) {
                            $frequency++;
                            if ($concept['frequency'] == $frequency) break;
                        }
                    }
                    if ($concept['frequency_operator'] == '' || $this->compare($frequency, $concept['frequency_operator'], $concept['frequency'])) {
                        $count++;
                    }
                }
            }
            return $count == count($rule['concepts']['SOCI']);
        }
        return true;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function ckVitals($rule)
    {
        if (isset($rule['concepts']['VITA']) && !empty($rule['concepts']['VITA'])) {
            $alerts_found = 0;

            foreach ($rule['concepts']['VITA'] as $concept) {
                $vitals = $this->Vitals->getVitalsByPid($this->Patient->getPatientPid());
                $codes = $this->Vitals->getCodes();
                $frequency = 0;

                foreach ($vitals as $vital) {
                    $mapping = $codes[$concept['concept_code']]['mapping'];
                    $isWithInterval = $concept['frequency_interval'] == '' ||
                        $this->isWithInterval($vital['date'], $concept['frequency_interval'], $concept['frequency_operator'], 'Y-m-d H:i:s');


                    if($isWithInterval && ($concept['value'] == '' || $this->compare($vital[$mapping], $concept['value_operator'], $concept['value']))){
                        $frequency++;
                    }

                }

                if (
                    $concept['frequency_operator'] == '' ||
                    $this->compare($frequency, $concept['frequency_operator'], $concept['frequency'])) {
                    $alerts_found++;
                }
            }

            return $alerts_found !== 0;
        }
        return true;
    }

    /**
     * @param $date
     * @param $interval
     * @param string $operator
     * @param string $dateFormat
     * @return bool
     */
    private function isWithInterval($date, $interval, $operator = '=', $dateFormat = 'Y-m-d')
    {
        if(!isset($interval[1])) return;
        $now = Carbon::now();
        $date = Carbon::createFromFormat($dateFormat, $date);
        switch (strtoupper($interval[1])) {
            case 'D':
	            $date->addDays($interval[0]);
                break;
            case 'M':
	            $date->addMonths($interval[0]);
                break;
            default:
	            $date->addYears($interval[0]);
        }
        return $now <= $date;
    }

    /**
     * @param $v1
     * @param $operator
     * @param $v2
     * @return bool
     */
    private function compare($v1, $operator, $v2)
    {
        switch ($operator) {
            case '!=':
                return $v1 != $v2;
            case '>=':
                return $v1 >= $v2;
            case '<=':
                return $v1 <= $v2;
            case '>':
                return $v1 > $v2;
            case '<':
                return $v1 < $v2;
            default:
                return $v1 == $v2;
        }
    }
}
