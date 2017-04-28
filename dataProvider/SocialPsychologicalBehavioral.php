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

class SocialPsychologicalBehavioral {

	/**
	 * @var MatchaCUP
	 */
	private $s;

	function __construct(){
		$this->s = MatchaModel::setSenchaModel('App.model.patient.SocialPsychologicalBehavioral', true);
	}

	public function getSocialPsychologicalBehaviors($params){
		return $this->s->load($params)->all();
	}

	public function getSocialPsychologicalBehavior($params){
		return $this->s->load($params)->one();
	}

	public function addSocialPsychologicalBehavior($params){
		return $this->s->save($params);
	}

	public function updateSocialPsychologicalBehavior($params){
		return $this->s->save($params);
	}

	public function destroySocialPsychologicalBehavior($params){
		return $this->s->destroy($params);
	}

}
