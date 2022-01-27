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

class PainScale {

	/**
	 * @var MatchaCUP
	 */
	private $ps;


	function __construct(){
		$this->ps = MatchaModel::setSenchaModel('App.model.patient.PainScale');
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getPainsScales($params){
		return $this->ps->load($params)->leftJoin(['service_date' => 'service_date'], 'encounters', 'eid', 'eid')->all();
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function getPainScale($params){
		return $this->ps->load($params)->one();
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function addPainScale($params){
		return $this->ps->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function updatePainScale($params){
		return $this->ps->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function destroyPainScale($params){
		return $this->ps->destroy($params);
	}

    /**
     * @param $eid
     * @return array|mixed
     */
	public function getPainScaleByEid($eid){
        return $this->getPainsScales(['eid' => $eid]);
	}
}
