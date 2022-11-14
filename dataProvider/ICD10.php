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
class ICD10 {

	/**
	 * @var bool|MatchaCUP
	 */
	private $i;

	function __construct() {
        if($this->i == NULL)
            $this->i = MatchaModel::setSenchaModel('App.model.administration.ICD10');
	}

	public function getICD10s($params){
		return $this->i->load($params)->all();
	}

	public function getICD10($params){
		return $this->i->load($params)->one();
	}

	public function addICD10($params){
		return $this->i->save($params);
	}

	public function updateICD10($params){
		return $this->i->save($params);
	}

	public function deleteICD10($params){
		return $this->i->destroy($params);
	}
}