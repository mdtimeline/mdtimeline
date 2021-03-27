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
class EncounterAddenda {

    /**
     * @var MatchaCUP
     */
    private $a;

    function __construct()
    {
        $this->a = MatchaModel::setSenchaModel('App.model.patient.encounter.Addendum');
    }

    public function getEncounterAddenda($params)
    {
        $records = $this->a->load($params)->leftJoin(
            [
            'fname' => 'created_by_fname',
            'mname' => 'created_by_mname',
            'lname' => 'created_by_lname'
        ], 'users', 'create_uid', 'id')->all();

        return $records;
    }

    public function getEncounterAddendum($params)
    {
        $record = $this->a->load($params)->leftJoin(
            [
                'fname' => 'created_by_fname',
                'mname' => 'created_by_mname',
                'lname' => 'created_by_lname'
            ], 'users', 'create_uid', 'id')
            ->one();

        return $record;
    }

    public function addEncounterAddendum($params) {
        return  $this->a->save($params);
    }

    public function updateEncounterAddendum($params) {
        return  $this->a->save($params);
    }

    public function destroyEncounterAddendum($params) {
        return  $this->a->destroy($params);
    }

    public function getEncounterAddendaByEid($eid) {
        return  $this->getEncounterAddenda(['eid' => $eid]);
    }

}
