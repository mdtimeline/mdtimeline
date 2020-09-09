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

class Locations {

	/**
	 * @var MatchaCUP
	 */
	private $l;

	function __construct(){
        $this->l = MatchaModel::setSenchaModel('App.model.administration.Location');
    }

	public function getLocations($params){
		return $this->l->load($params)->all();
	}

	public function getLocation($params){
		return $this->l->load($params)->all();
	}

	public function addLocation($params){
		return $this->l->save($params);
	}

	public function updateLocation($params){
		return $this->l->save($params);
	}

	public function deleteLocation($params){
		return $this->l->destroy($params);
	}

}
