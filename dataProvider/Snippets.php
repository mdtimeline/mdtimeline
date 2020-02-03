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
class Snippets {

	/**
	 * @var MatchaCUP
	 */
	private $s;

	function __construct(){
		$this->s = MatchaModel::setSenchaModel('App.model.administration.EncounterSnippet');
	}

	public function getSoapSnippets($params){
		$this->s->setOrFilterProperties(['uid']);
		unset($params->group);
		return $this->s->load($params)->all();
	}

	public function addSoapSnippets($params){
		return $this->s->save($params);
	}

	public function updateSoapSnippets($params){
		return $this->s->save($params);
	}

	public function deleteSoapSnippets($params){
		return $this->s->destroy($params);
	}
}
