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

include_once(ROOT . '/classes/Network.php');

class AuditLog {

	/**
	 * @var MatchaCUP
	 */
	public $a;

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
		$obj->ip = Network::getIpAddress();
		return $this->a->save($obj);
    }

	public function getLog($params) {

		if(isset($params->site) && isset($GLOBALS['worklist_dbs'][$params->site])){
			\Matcha::$__conn = null;
			\Matcha::connect($GLOBALS['worklist_dbs'][$params->site]);

			$this->a->setExtraValues([
				"{$params->site}" => 'site'
			]);
		}


        if(isset($params->filters)){


            $filters = $params->filters;

            $from_date = $filters->begin_date . ' ' . $filters->begin_time;
            $to_date = $filters->end_date . ' ' . $filters->end_time;

            $values = [
                ':from_date' => $from_date,
                ':to_date' => $to_date
            ];

            $sql = 'SELECT * FROM `audit_log` WHERE `event_date` >= :from_date AND `event_date` <= :to_date';

            if (isset($filters->table_name) && $filters->table_name != '') {
                $sql .= ' AND `foreign_table` = :table_name';
                $values[':table_name'] = $filters->table_name;
            }

            if (isset($filters->event_type) && $filters->event_type != '') {
                $sql .= ' AND `event` = :event';
                $values[':event'] = $filters->event_type;
            }

            if (isset($filters->pid) && $filters->pid != '') {
                $sql .= ' AND `pid` = :pid';
                $values[':pid'] = $filters->pid;
            }

            if (isset($filters->uid) && $filters->uid != '') {
                $sql .= ' AND `uid` = :uid';
                $values[':uid'] = $filters->uid;
            }

            $sql = "SELECT atl.*,
						u.title AS user_title,
						u.fname AS user_fname,
						u.mname AS user_mname,
						u.lname AS user_lname,
						p.title AS patient_title,
						p.fname AS patient_fname,
						p.mname AS patient_mname,
						p.lname AS patient_lname						
				  FROM ($sql) as atl
 			 LEFT JOIN users as u ON u.id = atl.uid
 			 LEFT JOIN patient as p ON p.pid = atl.pid";

            if(isset($params->sort)){
                $sorters = [];

                foreach ($params->sort as $sort){

                    if(!isset($sort->property)) continue;
                    if(!isset($sort->direction)) $sort->direction = 'ASC';

                    if($sort->property == 'patient_lname'){
                        $sort->property = 'p.lname';

                    }elseif($sort->property == 'user_lname'){
                        $sort->property = 'u.lname';
                    }else {
                        $sort->property = 'atl.' . $sort->property;
                    }

                    $sorters[] = "{$sort->property} {$sort->direction}";
                }

                if(!empty($sorters)){
                    $sorters = ' ORDER BY ' . implode(', ', $sorters);
                    $sql .= $sorters;
                }

            }

            $results = $this->a->sql($sql)->all($values);


        }else{
            $results =  $this->a->load($params)
                ->leftJoin(
                    [
                        'fname' => 'user_fname',
                        'mname' => 'user_mname',
                        'lname' => 'user_lname'
                    ],
                    'users',
                    'uid',
                    'id'
                )
                ->leftJoin(
                    [
                        'fname' => 'patient_fname',
                        'mname' => 'patient_mname',
                        'lname' => 'patient_lname'
                    ],
                    'patient',
                    'pid',
                    'pid'
                )
                ->all();
        }


		if(isset($params->site) && isset($GLOBALS['worklist_dbs'][$params->site])){
			\Matcha::$__conn = null;
			\Matcha::connect([
				'host' => site_db_host,
				'port' => site_db_port,
				'name' => site_db_database,
				'user' => site_db_username,
				'pass' => site_db_password,
				'app' => ROOT . '/app'
			]);
		}

		return $results;
	}

	public function getLogByEventName($params) {

		if(isset($params->site) && isset($GLOBALS['worklist_dbs'][$params->site])){
			\Matcha::$__conn = null;
			\Matcha::connect($GLOBALS['worklist_dbs'][$params->site]);

			$this->a->setExtraValues([
				"{$params->site}" => 'site'
			]);
		}

		$this->a->clearFilters();
		$this->a->addFilter('foreign_id', $params->foreign_id);
		$this->a->addFilter('foreign_table', $params->foreign_table);
		if(isset($params->event)){
			$this->a->addFilter('event', $params->event);
		}
		$results = $this->a->load()->leftJoin(
			[
				'fname' => 'user_fname',
				'mname' => 'user_mname',
				'lname' => 'user_lname'
			],
			'users',
			'uid',
			'id'
		)->all();


		if(isset($params->site) && isset($GLOBALS['worklist_dbs'][$params->site])){
			\Matcha::$__conn = null;
			\Matcha::connect([
				'host' => site_db_host,
				'port' => site_db_port,
				'name' => site_db_database,
				'user' => site_db_username,
				'pass' => site_db_password,
				'app' => ROOT . '/app'
			]);
		}

		return $results;
	}

	public function getLogByEventNames($params) {

		if(isset($params->site) && isset($GLOBALS['worklist_dbs'][$params->site])){
			\Matcha::$__conn = null;
			\Matcha::connect($GLOBALS['worklist_dbs'][$params->site]);

			$this->a->setExtraValues([
				"{$params->site}" => 'site'
			]);
		}

		$this->a->clearFilters();
		$this->a->addFilter('foreign_id', $params->foreign_id);
		$this->a->addFilter('foreign_table', $params->foreign_table);
		if(isset($params->events)){

			$this->a->setOrFilterProperties(['event']);
			foreach($params->events as $event){
				$this->a->addFilter('event', $event);
			}
		}

		if(isset($params->site) && isset($GLOBALS['worklist_dbs'][$params->site])){
			\Matcha::$__conn = null;
			\Matcha::connect([
				'host' => site_db_host,
				'port' => site_db_port,
				'name' => site_db_database,
				'user' => site_db_username,
				'pass' => site_db_password,
				'app' => ROOT . '/app'
			]);
		}

		return $this->a->load()->all();
	}
}
