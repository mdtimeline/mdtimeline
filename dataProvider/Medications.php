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
class Medications
{
    /**
     * @var MatchaCUP
     */
    private $m;
    /**
     * @var MatchaCUP
     */
    private $a;

    /**
     * @var PDO
     */
    private $db;

    function __construct()
    {
        if (!isset($this->m))
            $this->m = MatchaModel::setSenchaModel('App.model.patient.Medications');
        $this->m->setOrFilterProperties(['id']);
        $this->db = Matcha::getConn();
    }

    private function setAdministerModel(){
    	if(!isset($this->a)) $this->a = MatchaModel::setSenchaModel('App.model.patient.MedicationAdministered');
	}

    public function getPatientMedications($params)
    {
        // Manage the active and inactive problems
        if(isset($params->active) && $params->active == true) {
	        $filter = new stdClass();
	        $filter->property = 'is_active';
	        $filter->value = 1;
            $params->filter[] = $filter;
            unset($filter, $params->active);
        }

	    if(isset($params->reconciled) && $params->reconciled == true) {
		    $filter = new stdClass();
		    $filter->property = 'reconciled';
		    $filter->operator = '!=';
		    $filter->value = 1;
		    $params->filter[] = $filter;

		    $params->group[0] = new stdClass();
		    $params->group[0]->property = 'RXCUI';
		    $params->group[1] = new stdClass();
		    $params->group[1]->property = 'begin_date';
		    //$params->group[1]->direction = 'DESC';

		    $group = new stdClass();
		    $group->group[0] = new stdClass();
		    $group->group[0]->property = 'RXCUI';

		    if(isset($params->sort)){
			    $sorts = new stdClass();
			    $sorts->sort =  $params->sort;
		    }else{
			    $sorts = null;
		    }

		    $sorts = isset($params->sort) ? $params->sort : null;
		    unset($params->sort);

		    return $this->m->load($params)
			    ->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
			    ->sort($sorts)
			    ->group($group, true)->all();
	    }

        return $this->m->load($params)
            ->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
            ->all();
    }

    public function getPatientMedicationsOrders($params)
    {
        $this->m->addFilter('date_ordered', null, '!=');
        if (isset($params->reconciled) && $params->reconciled == true) {
            $groups = new stdClass();
            $groups->group[0] = new stdClass();
            $groups->group[0]->property = 'RXCUI';

            return $this->m->load($params)
                ->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
                ->group($groups)->all();
        }

        return $this->m->load($params)
            ->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
            ->all();
    }

    public function getPatientMedication($params)
    {
        return $this->m->load($params)
            ->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
            ->one();
    }

    public function addPatientMedication($params)
    {
    	if(is_array($params)){
			foreach($params as &$param){
				$param->create_date = date('Y-m-d H:i:s');
			}
	    }else{
		    $params->create_date = date('Y-m-d H:i:s');
	    }

        return $this->m->save($params);
    }

    public function updatePatientMedication($params)
    {
        return $this->m->save($params);
    }

    public function destroyPatientMedication($params)
    {
        return $this->m->destroy($params);
    }

    public function getPatientMedicationsByPid($pid, $reconciled = false)
    {
        $this->m->addFilter('pid', $pid);
        if ($reconciled) {
            $groups = new stdClass();
            $groups->group[0] = new stdClass();
            $groups->group[0]->property = 'RXCUI';
            return $this->m->load()
                ->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
                ->group($groups)
                ->all();
        }
        return $this->m->load()
            ->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
            ->all();
    }

	public function getPatientActiveMedicationsByPid($pid, $reconciled = false)
	{
		$this->m->addFilter('pid', $pid);
		$this->m->addFilter('is_active', 1);

		if ($reconciled) {
			$groups = new stdClass();
			$groups->group[0] = new stdClass();
			$groups->group[0]->property = 'RXCUI';
			return $this->m->load()
				->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
				->group($groups)
				->all();
		}
		return $this->m->load()
			->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
			->all();
	}

	public function getPatientActiveMedicationsByPidAndDates($pid, $reconciled = false, $start = null, $end = null)
	{
		$this->m->addFilter('pid', $pid);
		$this->m->addFilter('is_active', 1);

		if(isset($start)){
			$this->m->addFilter('created_date', $start, '>=');
		}
		if(isset($end)) {
			$this->m->addFilter('created_date', $end, '<=');
		}


		if ($reconciled) {
			$groups = new stdClass();
			$groups->group[0] = new stdClass();
			$groups->group[0]->property = 'RXCUI';
			return $this->m->load()
				->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
				->group($groups)
				->all();
		}
		return $this->m->load()
			->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')
			->all();
	}

    public function getPatientMedicationsActiveAt($pid, $date){
        $sql = "SELECT *
                FROM patient_medications AS pm
                LEFT JOIN users AS u ON pm.administered_uid = u.id
                WHERE pm.pid = :pid AND
                (
                    (:service_date_1 BETWEEN DATE(begin_date) AND DATE(end_date)) OR
                    (DATE(begin_date) = DATE(:service_date_2)) OR 
                    (
                        DATE(begin_date) <= DATE(:service_date_3) AND
                        end_date IS NULL AND 
                        is_active = 1
                    )
                )
                GROUP BY RXCUI";

                return $this->m->sql($sql)->all([
                    ':pid' => $pid,
                    ':service_date_1' => $date,
                    ':service_date_2' => $date,
                    ':service_date_3' => $date
                ]);
    }

    public function getPatientMedicationsByEid($eid)
    {
        $this->m->addFilter('eid', $eid);
        return $this->m->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();
    }

	public function getPatientMedicationsNotOrdersByEid($eid)
	{
		$this->m->addFilter('date_ordered', null, '!=');
		$this->m->addFilter('eid', $eid);
		return $this->m->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();
	}

	public function getPatientMedicationsOrdersByEid($eid)
	{
		$this->m->addFilter('date_ordered', null, '!=');
		$this->m->addFilter('eid', $eid);
		return $this->m->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();
	}

    public function getPatientAdministeredMedicationsByPid($pid, $eid)
    {
        $this->m->addFilter('pid', $pid);
        $this->m->addFilter('administered_uid', null, '!=');
        $this->m->addFilter('administered_uid', 0, '!=');
        return $this->m->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();
    }

    public function getPatientActiveMedicationsByPidAndCode($pid, $code)
    {
        $this->m->addFilter('pid', $pid);
        $this->m->addFilter('RXCUI', $code);
        $records = $this->m->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();

        foreach ($records as $i => $record) {
        	if(
		        isset($record['end_date']) &&
        		$record['end_date'] != '0000-00-00' &&
		        strtotime($record['end_date']) < strtotime(date('Y-m-d'))
	        ){
				// not active...
		        unset($records[$i]);
	        }
        }

        return $records;
    }


	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function getPatientMedicationsAdministered($params){
		$this->setAdministerModel();

		if(isset($params->include_not_administered) && $params->include_not_administered){

			$administered_records = $this->a->load($params)
				->leftJoin(
					[
						'title' => 'administered_title',
						'fname' => 'administered_fname',
						'mname' => 'administered_mname',
						'lname' => 'administered_lname',
					],
					'users', 'administered_uid', 'id'
				)
				->all();
			$administer_order_ids = [];

			foreach($administered_records as $administered_record){
				$administer_order_ids[] = $administered_record['order_id'];
			}

			$administer_order_ids = implode(',', $administer_order_ids);

			if($administer_order_ids != ''){
				$administer_order_ids = " AND id NOT IN  ({$administer_order_ids})";
			}

			$not_administered_meds = $this->m->sql("SELECT * FROM patient_medications WHERE eid = :eid {$administer_order_ids} AND administer_in_house = 1")
				->all([':eid' => $params->eid]);

			foreach($not_administered_meds as $not_administered_med){


				array_unshift($administered_records, [
					'pid' => $not_administered_med['pid'],
					'eid' => $not_administered_med['eid'],
					'order_id' => $not_administered_med['id'],
					'rxcui' => $not_administered_med['RXCUI'],
					'description' => $not_administered_med['STR'],
					'instructions' => $not_administered_med['directions'],
				]);

			}

			return $administered_records;

		} else{
			return $this->a->load($params)->leftJoin(
				[
					'title' => 'administered_title',
					'fname' => 'administered_fname',
					'mname' => 'administered_mname',
					'lname' => 'administered_lname',
				],
				'users', 'administered_uid', 'id'
			)->all();
		}

	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function getPatientMedicationAdministered($params){
		$this->setAdministerModel();
    	return $this->a->load($params)->leftJoin(
		    [
			    'title' => 'administered_title',
			    'fname' => 'administered_fname',
			    'mname' => 'administered_mname',
			    'lname' => 'administered_lname',
		    ],
		    'users', 'administered_uid', 'id'
	    )->one();
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function addPatientMedicationAdministered($params){
		$this->setAdministerModel();
    	return $this->a->save($params);
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function updatePatientMedicationAdministered($params){
		$this->setAdministerModel();
		return $this->a->save($params);
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function destroyPatientMedicationAdministered($params){
		$this->setAdministerModel();
		return $this->a->destroy($params);
	}

	public function getPatientAdministeredMedicationsByPidAndEid($pid, $eid)
	{
		$this->setAdministerModel();
		$this->a->addFilter('pid', $pid);
		if($eid) $this->a->addFilter('eid', $eid);
		$this->a->addFilter('administered', 1);
		$this->a->addFilter('administered_date', null, '!=');
		return $this->a->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();
	}

}

