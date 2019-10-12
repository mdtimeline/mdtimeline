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

class SocialHistory {

	/**
	 * @var MatchaCUP
	 */
	private $s = null;
	/**
	 * @var MatchaCUP
	 */
	private $ss = null;

	/**
	 * Set Model App.model.patient.PatientsOrders
	 */
	private function setModel(){
		if(!isset($this->s))
			$this->s = MatchaModel::setSenchaModel('App.model.patient.PatientSocialHistory');
		if(!isset($this->ss))
			$this->ss = MatchaModel::setSenchaModel('App.model.patient.SmokeStatus');
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getSocialHistories($params){
		$this->setModel();
		return $this->s->load($params)->all();
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getSocialHistory($params){
		$this->setModel();
		return $this->s->load($params)->one();
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function addSocialHistory($params){
		$this->setModel();
		return $this->s->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function updateSocialHistory($params){
		$this->setModel();
		return $this->s->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function destroySocialHistory($params){
		$this->setModel();
		return $this->s->destroy($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function getSmokeStatus($params){
		$this->setModel();
		return $this->ss->load($params)->all();
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function addSmokeStatus($params){
		$this->setModel();
		return $this->ss->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function updateSmokeStatus($params){
		$this->setModel();
		return $this->ss->save($params);
	}


	/**
	 * @param $params
	 * @return mixed
	 */
	public function destroySmokeStatus($params){
		$this->setModel();
		return $this->ss->destroy($params);
	}

    /**
     * @param $eid
     * @return mixed
     */
    public function getLastSmokeStatusByEid($eid){
        $this->setModel();
        $this->ss->addFilter('eid', $eid);
        return $this->ss->load()->one();
    }

	/**
	 * @param $pid
	 * @param null $start
	 * @param null $end
	 * @return mixed
	 */
	public function getSmokeStatusByPidAndDates($pid, $start = null, $end = null){
		$this->setModel();
		$this->ss->addFilter('pid', $pid);

		// TODO Change to create_date
		if(isset($start)){
			$this->ss->addFilter('start_date', $start, '>=');
		}
		if(isset($end)) {
			$this->ss->addFilter('start_date', $end, '<=');
		}
		return $this->ss->load()->all();
	}

	/**
	 * @param $pid
	 * @param null $start
	 * @param null $end
	 * @return mixed
	 */
	public function getSocialHistoriesByPidAndDates($pid, $start = null, $end = null){
		$this->setModel();
		$this->s->addFilter('pid', $pid);

		if(isset($start)){
			$this->s->addFilter('start_date', $start, '>=');
		}
		if(isset($end)) {
			$this->s->addFilter('start_date', $end, '<=');
		}
		return $this->s->load()->all();
	}

	/**
	 * @param $eid
	 * @param $code
	 * @return mixed
	 */
	public function getSocialHistoryByEidAndCode($eid, $code = 'history'){
		$this->setModel();
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'eid';
		$filters->filter[0]->value = $eid;
		if($code == 'smoking_status'){
			return $this->getSmokeStatus($filters);
		}else{
			return $this->getSocialHistories($filters);
		}
	}

	/**
	 * @param $pid
	 * @param $code
	 * @return mixed
	 */
	public function getSocialHistoryByPidAndCode($pid, $code = 'history'){
		$this->setModel();
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[0]->property = 'pid';
		$filters->filter[0]->value = $pid;
		if($code == 'smoking_status'){
			return $this->getSmokeStatus($filters);
		}else{
			return $this->getSocialHistories($filters);
		}
	}


}
