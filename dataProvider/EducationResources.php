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

class EducationResources {

	/**
	 * @var MatchaCUP
	 */
	private $e;

	function __construct() {
		$this->e = MatchaModel::setSenchaModel('App.model.administration.EducationResource');
	}

	public function getEducationResources($params) {
		return $this->e->load($params)->all();
	}

	public function getEducationResource($params) {
		return $this->e->load($params)->one();
	}

	public function addEducationResource($params) {
		return $this->e->save($params);
	}

	public function updateEducationResource($params) {
		return $this->e->save($params);
	}

	public function destroyEducationResource($params) {
		return $this->e->destroy($params);
	}

}