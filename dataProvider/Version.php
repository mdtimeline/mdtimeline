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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Version {
    /**
     * @var MatchaCUP
     */
    private $v;

    private $va;

    function __construct(){
        if(!isset($this->v))
            $this->v = MatchaModel::setSenchaModel('App.model.administration.Version');
        if(!isset($this->va))
            $this->va = MatchaModel::setSenchaModel('App.model.administration.VersionAcknowledge');
    }

    public function getLatestUpdate(){
        $sql = "SELECT *
                FROM version
                WHERE v_module = ''
                ORDER BY v_major DESC, v_minor DESC, v_patch DESC limit 1";

        return $this->v->sql($sql)->one();
    }

    public function getModuleLatestUpdate($module){
        $sql = "SELECT *,
                CONCAT(v_major, '.', v_minor, '.', v_patch) as 'full_version'
                FROM version
                WHERE v_module = '{$module}'
                ORDER BY v_major DESC, v_minor DESC, v_patch DESC limit 1";

        return $this->v->sql($sql)->one();
    }

    public function getAllModuleUpdates($module){
        $sql = "SELECT *,
                CONCAT(v_major, '.', v_minor, '.', v_patch) as 'full_version'
                FROM version
                WHERE v_module = '{$module}'
                ORDER BY v_major ASC, v_minor ASC, v_patch ASC";

        return $this->v->sql($sql)->all();
    }

    public function getUpdateAcknowledge($version, $userId){
        $sql = "SELECT *
                FROM version_acknowledge as acknowledge
                WHERE acknowledge.version = :version AND acknowledge.user_id = :userID";



        return $this->va->sql($sql)->one(['version' => $version, 'userID' => $userId]);
    }

    public function setUpdateAcknowledge($version, $userId){
        return $this->va->save((Object)['version' => $version, 'user_id' => $userId]);
    }


}