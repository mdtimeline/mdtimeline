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

class Reports
{

	/**
	 * @var bool|MatchaCUP
	 */
	private $r;

	function __construct()
	{
		$this->r = MatchaModel::setSenchaModel('App.model.reports.Report');
		return;
	}

	public function getReports($params){
		unset($params->group);

        $user_perms = ACL::getAllUserPermsAccess();
        $access_perm_list = [];

        foreach ($user_perms as $user_perm){
            $access_perm_list[] = $user_perm['perm'];
        }

        $access_perm_list = implode(',', $access_perm_list);
        $sql = "SELECT * FROM reports AS r WHERE r.report_perm = '*' OR FIND_IN_SET(r.report_perm, '{$access_perm_list}')";
		return $this->r->sql($sql)->all();
	}

	public function getReport($params, $include_parameters = true){
		$result = $this->r->load($params)->one();

		if($result === false || $include_parameters === false) return $result;

		$conn = Matcha::getConn();
		$sth = $conn->prepare("SELECT * FROM information_schema.parameters WHERE SPECIFIC_SCHEMA = ? AND SPECIFIC_NAME = ? AND PARAMETER_MODE = 'IN' ORDER BY ORDINAL_POSITION");
		$sth->execute([site_db_database, $result['store_procedure_name']]);
		$result['parameters'] = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $result;

	}

	public function addReport($params){
		return $this->r->save($params);
	}

	public function updateReport($params){
		return $this->r->save($params);
	}

	public function deleteReport($params){
		return $this->r->save($params);
	}

	public function runReportByIdAndFilters($report_id, $filters){

		$report = $this->getReport(['id' => $report_id]);

		if($report ===false){
			return [
				'success' => false,
				'error' => 'Report not Found'
			];
		}

		if(!isset($report['store_procedure_name']) || $report['store_procedure_name'] === ''){
			return [
				'success' => false,
				'error' => 'Report Store Procedure invalid'
			];
		}

		if(!isset($report['parameters']) || count($report['parameters']) === 0){
			return [
				'success' => false,
				'error' => 'Store Procedure not found for ' . $report['store_procedure_name']
			];
		}

		$conn = Matcha::getConn();

		$procedure_params = [];
		foreach ($filters as $property => $value){
			$procedure_params[':' . $property] = $value;
		}

		$procedure_params_keys = implode(', ',  array_keys($procedure_params));
		$sql = "CALL `{$report['store_procedure_name']}`( {$procedure_params_keys} )";
		$sth = $conn->prepare($sql);
		$sth->execute($procedure_params);
		$results = $sth->fetchAll();

		return $results;

	}



}
