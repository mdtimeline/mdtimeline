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

class AppState {

    /**
     * @var MatchaCUP
     */
    public $s;

    function __construct() {
        $this->s = MatchaModel::setSenchaModel('App.model.administration.UserSetting');
    }

    /**
     * @param $params
     * @return array
     */
	public function AppStateGet($params) {
	    if(isset($_SESSION['user']['id'])){
            $this->s->addFilter('uid', $_SESSION['user']['id']);
            return $this->s->load()->all();
        }else{
	        return [];
        }

	}

	public function AppStateSet($params){
	    $uid = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
	    if(is_array($params)){
	        foreach ($params as &$param){
                $param->uid = $uid;
            }
        }else{
            $params->uid = $uid;
        }
        return $this->s->save($params);
	}

	public function AppStateUnSet($params){
        return $this->s->destroy($params);
	}


}
