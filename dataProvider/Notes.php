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

class Notes {

	/**
	 * @var MatchaCUP
	 */
	private $n;

	function __construct(){
        if(!isset($this->n))
            $this->n = MatchaModel::setSenchaModel('App.model.patient.Notes');
	}

	public function getNotes($params){
		return $this->n->load($params)
            ->leftJoin(
                [
                    'fname' => 'user_fname',
                    'mname' => 'user_mname',
                    'lname' => 'user_lname',
                ],
                'users', 'uid', 'id'
            )->leftJoin(
                [
                    'fname' => 'update_user_fname',
                    'mname' => 'update_user_mname',
                    'lname' => 'update_user_lname',
                ],
                'users', 'update_uid', 'id'
            )->all();
	}

	public function getNote($params){
		return $this->n->load($params)
            ->leftJoin(
                [
                    'fname' => 'user_fname',
                    'mname' => 'user_mname',
                    'lname' => 'user_lname',
                ],
                'users', 'uid', 'id'
            )->leftJoin(
                [
                    'fname' => 'update_user_fname',
                    'mname' => 'update_user_mname',
                    'lname' => 'update_user_lname',
                ],
                'users', 'update_uid', 'id'
            )->one();
	}

	public function addNote($params){
		return $this->n->save($params);
	}

	public function updateNote($params){
		return $this->n->save($params);
	}

	public function destroyNote($params){
		return $this->n->destroy($params);
	}



} 