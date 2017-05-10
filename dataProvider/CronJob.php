<?php
/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, LLC.
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

include_once(ROOT . '/classes/Sessions.php');
include_once(ROOT . '/dataProvider/Patient.php');

class CronJob
{

    /**
     * @var bool|MatchaCUP
     */
    private $CronJobModel;

    function __construct() {
        if($this->CronJobModel == NULL)
            $this->CronJobModel = MatchaModel::setSenchaModel('App.model.administration.CronJob');
    }

    public function getCronJob($params){
        $records = $this->CronJobModel->load($params)->all();
        error_log(print_r($records,true));
        foreach($records['data'] as $index => $record){
            if($record['running']){
                $records['data'][$index]['elapsed'] = time() - strtotime($record['last_run_date']);
            } else {
                $records['data'][$index]['elapsed'] = '';
            }
        }
        return $records;
    }

    public function updateCronJob($params){
        return $this->CronJobModel->save($params);
    }

}
