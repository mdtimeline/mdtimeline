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
include_once(ROOT . '/dataProvider/ACL.php');
include_once(ROOT . '/dataProvider/FloorPlansRules.php');
include_once(ROOT . '/dataProvider/PatientZone.php');

class PoolArea
{

    /**
     * @var PDO
     */
    private $conn;

    /**
     * @var Patient
     */
    private $patient;

    /**
     * @var FloorPlansRules
     */
    private $FloorPlansRules;

    /**
     * @var
     */
    private $acl;

    /**
     * @var MatchaCUP
     */
    private $pa;

    /**
     * @var MatchaCUP
     */
    private $pp;

    function __construct()
    {
        $this->conn = \Matcha::getConn();
    }


    private function setPatient()
    {
        if (!isset($this->patient)) {
            $this->patient = new Patient();
        }
    }

    private function setFloorPlansRules()
    {
        if (!isset($this->FloorPlansRules)) {
            $this->FloorPlansRules = new FloorPlansRules();
        }
    }

    private function setPaModel()
    {
        if (!isset($this->pa))
            $this->pa = MatchaModel::setSenchaModel('App.model.areas.PoolArea');
    }

    private function setPpModel()
    {
        if (!isset($this->pp))
            $this->pp = MatchaModel::setSenchaModel('App.model.areas.PatientPool');
    }

    public function getPatientsArrivalLog(stdClass $params)
    {
        $this->setPatient();
        $this->setPaModel();
        $visits = [];
        foreach ($this->getPatientParentPools() AS $visit) {
            $id = $visit['id'];
            $foo = $this->pa->sql("SELECT pp.id, pa.title AS area, pp.time_out, pp.eid
								 FROM patient_pools AS pp
						    LEFT JOIN pool_areas AS pa ON pp.area_id = pa.id
							    WHERE pp.parent_id = '$id'
							 ORDER BY pp.id DESC")->one();
            $visit['area'] = $foo['area'];
            $visit['area_id'] = $foo['id'];
            $visit['name'] = ($foo['eid'] != null ? '*' : '') . $this->patient->getPatientFullNameByPid($visit['pid']);
            $visit['warning'] = $this->patient->getPatientArrivalLogWarningByPid($visit['pid']);
            $visit['warningMsg'] = ($visit['warning'] ? 'Patient "Sex" or "Date Of Birth" not set' : '');
            if ($foo['time_out'] == null) {
                $visits[] = $visit;
            }
        }
        return $visits;
    }

    private function getPatientParentPools()
    {
        $this->setPaModel();
        $sql = "SELECT pp.id, pp.time_in AS time, pp.pid
				 FROM patient_pools AS pp
            LEFT JOIN pool_areas AS pa ON pp.area_id = pa.id
			    WHERE pp.id = pp.parent_id
			      AND pa.facility_id = '{$_SESSION['user']['facility']}'
			 ORDER BY pp.time_in ASC, pp.priority DESC
			    LIMIT 500";
        $parentPools = $this->pa->sql($sql)->all();
        return $parentPools;
    }

    private function getParentPoolId($id)
    {
        $this->setPaModel();
        $foo = $this->pa->sql("SELECT parent_id FROM patient_pools WHERE id = '$id'")->one();
        return $foo !== false ? $foo['parent_id'] : 0;
    }

    public function addPatientArrivalLog($params)
    {
        $this->setPatient();
        if ($params->isNew) {
            $patient = $this->patient->createNewPatientOnlyName($params->name);
            $params->pid = $patient['patient']['pid'];
            $params->area = 'Check In';
            $params->area_id = 1;
            $params->new = true;
            $params->warning = true;
            $this->checkInPatient($params);
        } else {
            $this->checkInPatient($params);
        }
        return $params;
    }

    public function updatePatientArrivalLog(stdClass $params)
    {
    }

    public function removePatientArrivalLog(stdClass $params)
    {
        $this->setPpModel();
        $record = new stdClass();
        $record->id = $params->area_id;
        $record->time_out = date('Y-m-d H:i:s');
        $this->pp->save($record);
        unset($record);
        return ['success' => true];
    }

    public function sendPatientToPoolArea(stdClass $params)
    {
        $this->setPpModel();
        $prevArea = $this->getCurrentPatientPoolAreaByPid($params->pid);

        $now = date('Y-m-d H:i:s');

        /**
         * If patient comes from another area check him/her out
         */
        if (!empty($prevArea)) {
            $record = new stdClass();
            $record->id = $prevArea['id'];
            $record->time_out = $now;
            $this->pp->save($record);

            // check out patient from any patient zone
            $sql = "UPDATE `patient_zone` SET `time_out` = :time_out WHERE `pid` = :pid AND `time_out` IS NULL";
            $sth = $this->conn->prepare($sql);
            $sth->execute([':time_out' => $record->time_out, ':pid' => $prevArea['pid']]);
            unset($record);
        }

        $record = new stdClass();
        $record->pid = $params->pid;
        $record->uid = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
        $record->time_in = $now;
        $record->area_id = $params->sendTo;
        $record->appointment_id = isset($params->appointment_id) ? $params->appointment_id : null;
        $record->in_queue = 1;
        $record->priority = (isset($params->priority) ? $params->priority : '');

        if (!empty($prevArea)) {
            $record->parent_id = $this->getParentPoolId($prevArea['id']);
            $record->eid = $prevArea['eid'];
            $record->appointment_id = $prevArea['appointment_id'];
            $record->priority = $prevArea['priority'];
        }
        $record = (object)$this->pp->save($record);

        if (empty($prevArea)) {
            $record->parent_id = $record->id;
            Matcha::pauseLog(true); // no need to log this
            $record = $this->pp->save($record);
            Matcha::pauseLog(false);
        }

        $this->setFloorPlansRules();
        $rule = $this->FloorPlansRules->getFloorPlansRuleByFloorPlanRecord((object)$record);

        $zone = null;
        if ($rule !== false && isset($rule['zone_id'])) {
            $PatientZone = new PatientZone();
            $buff = (object)[
                'pid' => $record->pid,
                'zone_id' => $rule['zone_id'],
                'provider_id' => $rule['provider_user_id'],
                'zone' => $rule['zone']
            ];
            $zone = $PatientZone->addPatientToZone($buff);
        }

        return ['record' => $record, 'floor_plan_id' => $this->getFloorPlanIdByPoolAreaId($record->area_id), 'zone' => $zone];

    }

    public function isPatientAlreadyCheckedIn($pid, $areaId, $appointmentId)
    {
        $this->setPpModel();
        $sql = "SELECT *
                FROM mdtimeline.patient_pools
                WHERE pid = {$pid} AND area_id = {$areaId} AND DATE(NOW()) = DATE(time_in) AND appointment_id = {$appointmentId}";
        $row = $this->pp->sql($sql)->one();
        return $row === false ? false : true;
    }

    public function getPoolAreaPatients(stdClass $params)
    {
        return $this->getPatientsByPoolAreaId($params->area_id, 1);
    }

    public function getFacilityActivePoolAreas()
    {
        $this->setPaModel();
        return $this->pa->sql("SELECT * FROM pool_areas	WHERE facility_id = '{$_SESSION['user']['facility']}' AND active = '1' ORDER BY sequence")->all();
    }

    public function getActivePoolAreas()
    {
        $this->setPaModel();
        return $this->pa->sql("SELECT pa.*,
			f.name as facility_name,
            fp.title as floor_plan_title
FROM pool_areas as pa
LEFT JOIN facility as f on pa.facility_id = f.id
LEFT JOIN floor_plans as fp on pa.floor_plan_id = fp.id
WHERE pa.active = '1'
ORDER BY sequence")->all();
    }

    public function getAreaByCode($area_code)
    {
        $this->setPaModel();
        return $this->pa->sql("SELECT * FROM pool_areas	WHERE `code` = :area_code AND active = '1'")->one([':area_code' => $area_code]);

    }

    /**
     * This this return an arrays of Areas
     * where array index equal the area ID
     */
    public function getAreasArray()
    {
        $areas = [];
        foreach ($this->getActivePoolAreas() as $area) {
            $areas[$area['id']] = $area;
        }
        return $areas;
    }

    /******************************************************************************************************************/
    /******************************************************************************************************************/
    /******************************************************************************************************************/
    private function checkInPatient($params)
    {
        $this->setPpModel();
        $record = new stdClass();
        $record->pid = $params->pid;
        $record->uid = $_SESSION['user']['id'];
        $record->time_in = date('Y-m-d H:i:s');
        $record->area_id = 1;
        $record->in_queue = 1;
        $this->pp->save($record);

        $record->parent_id = $record->id;
        Matcha::pauseLog(true);
        $this->pp->save($record);
        Matcha::pauseLog(false);
    }

    public function getCurrentPatientPoolAreaByPid($pid)
    {
        $this->setPpModel();
        $record = $this->pp->sql("SELECT pp.*, pa.title AS poolArea
								 	FROM patient_pools AS pp
							   LEFT JOIN pool_areas AS pa ON pa.id = pp.area_id
								   WHERE pp.pid = '$pid'
								  	 AND pp.time_out IS NULL
							 	ORDER BY pp.id DESC")->one();
        return $record;
    }

    public function updateCurrentPatientPoolAreaByPid($data, $pid)
    {
        $this->setPpModel();
        $area = $this->getCurrentPatientPoolAreaByPid($pid);
        $data['id'] = $area['id'];
        $this->pp->save((object)$data);
        return;
    }

    private function getPatientsByPoolAreaId($area_id, $in_queue)
    {
        $this->setPatient();
        $this->setPpModel();
        return $this->pp->sql("SELECT `pp`.*,
                                        `p`.pubpid,
                                        `p`.fname,
                                        `p`.lname,
                                        `p`.mname,
                                        `fpz`.title as zone,
                                        `ae`.start as appointment_time
								  FROM (SELECT *
								          FROM `patient_pools` AS pp WHERE pp.area_id = '$area_id'
								           AND pp.time_out IS NULL
								           AND pp.in_queue = '$in_queue'
								       ) AS pp
							 INNER JOIN `patient` AS p ON pp.pid = p.pid
						     LEFT JOIN `patient_zone` AS pz ON pz.pid = pp.pid AND pz.time_out IS NULL
						     LEFT JOIN `floor_plans_zones` AS fpz ON fpz.id = pz.zone_id
						     LEFT JOIN `appointments_events` AS ae ON ae.id = pp.appointment_id
							   ORDER BY `ae`.start")->all();

    }

    /**
     * Form now this is just getting the latest open encounter for all the patients.
     *
     * @param $params
     *
     * @return array
     */
    public function getPatientsByPoolAreaAccess($params)
    {
        Matcha::pauseLog(true);
        if (is_numeric($params)) {
            $uid = $params;
        } elseif (!is_numeric($params) && isset($params->eid)) {
            $uid = $params->eid;
        } elseif (!isset($_SESSION['user']['id'])) {
            return [];
        } else {
            $uid = $_SESSION['user']['id'];
        }

        $zones_inner_join = '';

        if (isset($params->zones) && !empty($params->zones)) {
            $zone_ids = implode(',', $params->zones);
            $zones_inner_join = "INNER JOIN `patient_zone` AS pz ON pz.pid = pp.pid AND pz.time_out IS NULL AND pz.zone_id IN ({$zone_ids})";
        }

        $this->acl = new ACL($uid);
        $pools = [];

        if ($this->acl->hasPermission('use_pool_areas')) {
            $this->setPatient();

            $activeAreas = $this->getFacilityActivePoolAreas();
            $areas = [];
            $pools = [];

            if (!empty($activeAreas)) {

                foreach ($activeAreas as $activeArea) {
                    if (($activeArea['concept'] == 'CHECKIN' && $this->acl->hasPermission('access_poolcheckin')) ||
                        ($activeArea['concept'] == 'TRIAGE' && $this->acl->hasPermission('access_pooltriage')) ||
                        ($activeArea['concept'] == 'PHYSICIAN' && $this->acl->hasPermission('access_poolphysician')) ||
                        ($activeArea['concept'] == 'CHECKOUT' && $this->acl->hasPermission('access_poolcheckout')) ||
                        ($activeArea['concept'] == 'TREATMENT' && $this->acl->hasPermission('access_pooltreatment'))
                    ) {
                        $areas[] = "pp.area_id = '{$activeArea['id']}'";
                    }
                }

                $whereAreas = '(' . implode(' OR ', $areas) . ')';

                $whereEncounters = '';

                if (!empty($_SESSION['user']['npi'])) {
                    $whereEncounters .= ' WHERE (enc.provider_uid IS NULL OR enc.provider_uid = ' . $_SESSION['user']['id'] . ')';
                }

                $sql = "SELECT pools.*,
							   enc.provider_uid
 						  FROM (
							SELECT pp.*,
	                               p.fname,
	                               p.lname,
	                               p.mname,
	                               IF(pp.eid IS NOT NULL, CONCAT('*', IF(CHAR_LENGTH(CONCAT(p.lname, ', ', p.fname)) > 15, CONCAT(LEFT(CONCAT(p.lname, ', ', p.fname), 15), '...'), CONCAT(p.lname, ', ', p.fname))),
	                                    IF(CHAR_LENGTH(CONCAT(p.lname, ', ', p.fname)) > 15, CONCAT(LEFT(CONCAT(p.lname, ', ', p.fname), 15), '...'), CONCAT(p.lname, ', ', p.fname))) as shortName,
	                               pa.title as poolArea
							  FROM `patient_pools` AS pp
						 LEFT JOIN `patient` AS p ON pp.pid = p.pid
						 LEFT JOIN `pool_areas` AS pa ON pp.area_id = pa.id
							{$zones_inner_join}
	                         WHERE {$whereAreas}
							   AND pp.time_out IS NULL
							   AND pp.in_queue = '1'
							) AS pools
						LEFT JOIN encounters AS enc ON enc.eid = pools.eid 
						{$whereEncounters} 
				      	ORDER BY pools.time_in
				        LIMIT 50";

                $patientPools = $this->pa->sql($sql)->all();

                $pools = [];
                foreach ($patientPools AS $patientPool) {
//					$patientPool['name'] = ($patientPool['eid'] != null ? '*' : '') . Person::fullname($patientPool['fname'], $patientPool['mname'], $patientPool['lname']);
//					$patientPool['shortName'] = Person::ellipsis($patientPool['name'], 15);
//					$patientPool['poolArea'] = $patientPool['title'];
                    $patientPool['patient'] = $this->patient->getPatientDemographicDataByPid($patientPool['pid']);
                    $patientPool['floorPlanId'] = $this->getFloorPlanIdByPoolAreaId($patientPool['area_id']);
                    $z = $this->getPatientCurrentZoneInfoByPid($patientPool['pid']);
                    $pools[] = (empty($z)) ? $patientPool : array_merge($patientPool, $z);
                }

            }
        }

        Matcha::pauseLog(false);
        return $pools;
    }

    public function getAreaTitleById($id)
    {
        $this->setPaModel();
        $area = $this->pa->sql("SELECT title FROM pool_areas WHERE id = '{$id}'")->one();
        return $area['title'];
    }

    public function getFloorPlanIdByPoolAreaId($poolAreaId)
    {
        $this->setPaModel();
        $area = $this->pa->sql("SELECT floor_plan_id FROM pool_areas WHERE id = '{$poolAreaId}'")->one();
        return $area['floor_plan_id'];
    }

    public function getPatientCurrentZoneInfoByPid($pid)
    {
        $this->setPpModel();
        $zone = $this->pp->sql("SELECT id AS patientZoneId,
								  zone_id AS zoneId,
								  time_in AS zoneTimeIn
		                     FROM patient_zone
		                    WHERE pid = '$pid' AND time_out IS NULL
		                    ORDER BY id DESC")->one();
        return $zone;
    }

}

//$e = new PoolArea();
//echo '<pre>';
//$params           = new stdClass();
//$params->pid      = 1;
//$params->priority = 'Immediate';
//$params->sendTo   = 3;
//print_r($e->sendPatientToPoolArea($params));
//print '<br><br>Session ----->>> <br><br>';
//print_r($_SESSION);
