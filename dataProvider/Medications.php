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
    	if(!isset($this->a)) $this->a = MatchaModel::setSenchaModel('App.model.patient.MedicationAdministered', true);
	}

    public function getPatientMedications($params)
    {
        // Manage the active and inactive problems
        if(isset($params->active) && $params->active == true) {
	        $filter = new stdClass();
	        $filter->property = 'end_date';
	        $filter->value = null;
            $params->filter[] = $filter;
            unset($filter, $params->active);
        }

	    if(isset($params->reconciled) && $params->reconciled == true) {
		    $filter = new stdClass();
		    $filter->property = 'reconciled';
		    $filter->operator = '!=';
		    $filter->value = 1;
		    $params->filter[] = $filter;
		    unset($filter, $params->reconciled);
	    }

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
        if(!isset($params)) return;
        $params->create_date = date('Y-m-d H:i:s');
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

	public function getPatientMedicationsByPidAndDates($pid, $reconciled = false, $start = null, $end = null)
	{
		$this->m->addFilter('pid', $pid);

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

    public function getPatientMedicationsByEid($eid)
    {
        $this->m->addFilter('eid', $eid);
        return $this->m->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();
    }

	public function getPatientMedicationsOrdersByEid($eid)
	{
		$this->m->addFilter('date_ordered', null, '!=');
		$this->m->addFilter('eid', $eid);
		return $this->m->load()->leftJoin(['title', 'fname', 'mname', 'lname'], 'users', 'administered_uid', 'id')->all();
	}

    public function getPatientActiveMedicationsByPid($pid, $reconciled = false)
    {
        $records = $this->getPatientMedicationsByPid($pid, $reconciled);
        foreach ($records as $i => $record) {
            if($record['administered_date'] != null){ unset($records[$i]); }
            if (
                $record['end_date'] == null ||
                $record['end_date'] == '0000-00-00' ||
                strtotime($record['end_date']) <= strtotime(date('Y-m-d'))
            ) continue;
            unset($records[$i]);
        }
        return $records;
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

