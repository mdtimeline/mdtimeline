<?php
/**
GaiaEHR (Electronic Health Records)
Copyright (C) 2013 Certun, LLC.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class AuditLog {

	/**
	 * @var MatchaCUP
	 */
	private $a;

	function __construct() {
		$this->a = MatchaModel::setSenchaModel('App.model.administration.AuditLog');
	}

	public function addLog($params) {

		if(isset($params->uid)){
			$uid = $params->uid;
		}elseif(isset($_SESSION['user']) && isset($_SESSION['user']['id'])){
			$uid = $_SESSION['user']['id'];
		}else{
			$uid = 0;
		}

		$obj = new stdClass();
		$obj->eid = isset($params->eid) ? $params->eid : 0;
		$obj->pid = isset($params->pid) ? $params->pid : 0;
		$obj->uid = $uid;
		// foreign id
		$obj->foreign_id = isset($params->foreign_id) ? $params->foreign_id : 0;
		$obj->foreign_table = isset($params->foreign_table) ? $params->foreign_table : '';
		$obj->event = isset($params->event) ? $params->event : '';
		$obj->event_description = isset($params->event_description) ? $params->event_description : '';
		$obj->event_date = date('Y-m-d H:i:s');
		return $this->a->save($obj);
    }

	public function getLogByEventName($params) {
		$this->a->clearFilters();
		$this->a->addFilter('foreign_id', $params->foreign_id);
		$this->a->addFilter('foreign_table', $params->foreign_table);
		if(isset($params->event)){
			$this->a->addFilter('event', $params->event);
		}
		return $this->a->load()->leftJoin(
			[
				'fname' => 'user_fname',
				'mname' => 'user_mname',
				'lname' => 'user_lname'
			],
			'users',
			'uid',
			'id'
		)->all();
	}

	public function getLogByEventNames($params) {
		$this->a->clearFilters();
		$this->a->addFilter('foreign_id', $params->foreign_id);
		$this->a->addFilter('foreign_table', $params->foreign_table);
		if(isset($params->events)){

			$this->a->setOrFilterProperties(['event']);
			foreach($params->events as $event){
				$this->a->addFilter('event', $event);
			}
		}
		return $this->a->load()->all();
	}
}
