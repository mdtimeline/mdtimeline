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
class TransactionLog
{

	/**
	 * @var MatchaCUP
	 */
	private $t;

	function __construct()
	{
		$this->t = MatchaModel::setSenchaModel('App.model.administration.TransactionLog', true);
	}

	public function search($params)
	{
		$filters = $params->filters;

		$from_date = $filters->begin_date . ' ' . $filters->begin_time;
		$to_date = $filters->end_date . ' ' . $filters->end_time;

		$values = [
			':from_date' => $from_date,
			':to_date' => $to_date
		];

		$sql = 'SELECT * FROM `audit_transaction_log` WHERE `date` >= :from_date AND `date` <= :to_date';

		if (isset($filters->table_name) && $filters->table_name != '') {
			$sql .= ' AND `table_name` = :table_name';
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

		return $this->t->sql($sql)->all($values);
	}

	public function getTransactionLog($params)
	{
		if (isset($params->filters)) {
			return $this->search($params);
		}
		return $this->t->load($params)->all();
	}

	public function saveExportLog($data)
	{
		$saveParams = [
			'event' => $data->event,
			'data' => [
				'pid' => $data->pid,
				'eid' => $data->eid
			]
		];
		MatchaHelper::storeAudit($saveParams);
		return [
			'success' => true
		];
	}

	public function saveTransactionLog($Log)
	{
		$saveParams = $Log;
		MatchaHelper::storeAudit($saveParams);
		return [
			'success' => true
		];
	}

	public function getTransactionLogDetailByTableAndPk($table, $pk){

		$conn = Matcha::getConn();

		// find table primary key
		$rec = $conn->query("SHOW INDEX FROM $table");
		$pk_column = $rec->fetch(PDO::FETCH_ASSOC);
		$pk_column = $pk_column['Column_name'];

		$sth = $conn->prepare("SELECT * FROM `{$table}` WHERE `{$pk_column}` = :pk");
		$sth->execute([ ':pk' => $pk ]);
		$record = $sth->fetch(PDO::FETCH_ASSOC);

		unset($rec, $pk_column);

		if($record === false){
			return [];
		}

		$columns = [];
		foreach ($record as $column => $value){
			$columns[] = $column;
		}

		$this->t->addFilter('table_name', $table);
		$this->t->addFilter('pk', $pk);
		$results = $this->t->load()->leftJoin(
			[
				'title' => 'user_title',
				'fname' => 'user_fname',
				'mname' => 'user_mname',
				'lname' => 'user_lname',
			],
			'users', 'uid', 'id'
		)->all();

		$records = [];

		foreach ($results as &$result){

			$record_buff = [];

			if(isset($result['id'])){
				$record_buff['_id'] = $result['id'];
			}elseif(isset($result['pid'])){
				$record_buff['_id'] = $result['pid'];
			}elseif(isset($result['pid'])){
				$record_buff['_id'] = $result['eid'];
			}

			$record_buff['_event_time'] = $result['date'];
			$record_buff['_event_type'] = $result['event'];
			$record_buff['_event_uid'] = $result['uid'];
			$record_buff['_event_user_title'] = $result['user_title'];
			$record_buff['_event_user_fname'] = $result['user_fname'];
			$record_buff['_event_user_mname'] = $result['user_mname'];
			$record_buff['_event_user_lname'] = $result['user_lname'];

			if(
				$result['event'] != 'UPDATE' &&
				$result['event'] != 'INSERT' &&
				isset($result['data']) &&
				is_array($result['data'])
			){
				unset($record_buff);
				continue;
			}

			foreach ($result['data'] as $col => $value){
				$record_buff[$col] = $value;
			}

			$records[] = $record_buff;
			unset($record_buff, $result['data']);
		}

		unset($result, $results);

		$record['_id'] = -1;
		$record['_event_time'] = date('Y-m-d H:i:s');
		$record['_event_type'] = 'CURRENT';
		$records[] = $record;

		unset($record);

		return [
			'table' => $table,
			'columns' => $columns,
			'total' => count($records),
			'data' => $records,
		];
	}

}
