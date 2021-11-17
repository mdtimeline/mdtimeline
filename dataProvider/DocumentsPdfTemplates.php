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

class DocumentsPdfTemplates {

	/**
	 * @var bool|MatchaCUP
	 */
	private $t;

	function __construct(){
        $this->t = \MatchaModel::setSenchaModel('App.model.administration.DocumentsPdfTemplate');
	}
	public function getDocumentsPdfTemplates($params){
		return $this->t->load($params)->all();
	}
	public function getDocumentsPdfTemplate($params){
		return $this->t->load($params)->one();
	}
	public function addDocumentsPdfTemplate($params){
		return $this->t->save($params);
	}
	public function updateDocumentsPdfTemplate($params){
		return $this->t->save($params);
	}
	public function destroyDocumentsPdfTemplate($params){
		return $this->t->destroy($params);
	}
}
