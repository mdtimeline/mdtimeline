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
class ActiveProblems
{

    /**
     * @var MatchaCUP
     */
    private $a;

    function __construct()
    {
        if ($this->a == null)
            $this->a = MatchaModel::setSenchaModel('App.model.patient.PatientActiveProblem');
    }

    public function getPatientActiveProblems($params)
    {
        // Manage the active and inactive problems
        if(isset($params->active) && $params->active == true) {
            $params->filter[1] = new stdClass();
            $params->filter[1]->property = 'end_date';
            $params->filter[1]->value = null;
            unset($params->active);
        }

        if (isset($params->reconciled) && $params->reconciled == true) {
            $groups = new stdClass();
            $groups->group[0] = new stdClass();
            $groups->group[0]->property = 'code';
            $records = $this->a->load($params)->group($groups)->all();
        } else {
            $records = $this->a->load($params)->all();
        }
        return array_values($records);
    }

    public function getPatientActiveProblem($params)
    {
        return $this->a->load($params)->one();
    }

    public function addPatientActiveProblem($params)
    {
        return $this->a->save($params);
    }

    public function updatePatientActiveProblem($params)
    {
        return $this->a->save($params);
    }

    public function destroyPatientActiveProblem($params)
    {
        return $this->a->destroy($params);
    }

    public function getPatientActiveProblemByPid($pid)
    {
        $params = new stdClass();
        $params->filter[0] = new stdClass();
        $params->filter[0]->property = 'pid';
        $params->filter[0]->value = $pid;
        $records = $this->a->load($params)->all();
        unset($params);
        foreach ($records as $i => $record) {
            if (strtotime($record['end_date']) &&
                strtotime($record['end_date']) <= strtotime(date('Y-m-d')) &&
                $record['end_date'] != '0000-00-00'
            ) {
                unset($records[$i]);
            }
        }
        return array_values($records);
    }

    public function getPatientAllProblemsByPid($pid)
    {
        $params = new stdClass();
        $params->filter[0] = new stdClass();
        $params->filter[0]->property = 'pid';
        $params->filter[0]->value = $pid;
        $records = $this->a->load($params)->all();
        unset($params);
        return $records;
    }

    public function getPatientActiveProblemByEid($eid)
    {
        $params = new stdClass();
        $params->filter[0] = new stdClass();
        $params->filter[0]->property = 'eid';
        $params->filter[0]->value = $eid;
        $records = $this->a->load($params)->all();
        unset($params);
        foreach ($records as $i => $record) {
            if (strtotime($record['end_date']) &&
                strtotime($record['end_date']) <= strtotime(date('Y-m-d')) &&
                $record['end_date'] != '0000-00-00'
            ) {
                unset($records[$i]);
            }
        }
        return array_values($records);
    }

    public function getPatientActiveProblemByPidAndCode($pid, $code)
    {
        $params = new stdClass();
        $params->filter[0] = new stdClass();
        $params->filter[0]->property = 'pid';
        $params->filter[0]->value = $pid;
        $params->filter[2] = new stdClass();
        $params->filter[2]->property = 'code';
        $params->filter[2]->value = $code;
        $records = $this->a->load($params)->all();
        unset($params);
        foreach ($records as $i => $record) {
            if (strtotime($record['end_date']) &&
                strtotime($record['end_date']) <= strtotime(date('Y-m-d')) &&
                $record['end_date'] != '0000-00-00'
            ) {
                unset($records[$i]);
            }
        }
        return array_values($records);
    }

}

