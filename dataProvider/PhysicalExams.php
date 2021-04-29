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
class PhysicalExams
{

    /**
     * @var MatchaCUP
     */
    private $e;

    function __construct()
    {
        $this->e = MatchaModel::setSenchaModel('App.model.patient.PhysicalExam');
    }

    public function getPhysicalExams($params)
    {
        return $this->e->load($params)->all();
    }

    public function getPhysicalExam($params)
    {
        return $this->e->load($params)->one();
    }

    public function addPhysicalExam($params)
    {
        return $this->e->save($params);
    }

    public function updatePhysicalExam($params)
    {
        return $this->e->save($params);
    }

    public function destroyPhysicalExam($params)
    {
        return $this->e->destroy($params);
    }

    public function getPhysicalExamsByEid($eid)
    {
        $this->e->addFilter('eid', $eid);
        return $this->e->load()->all();
    }

}
