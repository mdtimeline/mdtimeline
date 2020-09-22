<?php

/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2012 Ernesto Rodriguez
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

class PrintJob
{

    /**
     * @var bool|\MatchaCUP
     */
    private $p;


    function __construct()
    {
        $this->p = \MatchaModel::setSenchaModel('App.model.administration.PrintJob');
    }

    public function getPrintJobs($params)
    {
        $this->p->setOrFilterProperties(['print_status', 'priority', 'uid']);
        return $this->p->load($params)->leftJoin(
            [
                'fname' => 'user_fname',
                'lname' => 'user_lname',
                'mname' => 'user_mname',
                'username' => 'user_username',
            ],
            'users',
            'uid',
            'id'
        )->leftJoin(
            [
                'docType' => 'document_doc_type',
                'title' => 'document_title',
                'note' => 'document_note'
            ],
            'patient_documents',
            'document_id',
            'id'
        )->all();
    }

    public function getPrintJob($params)
    {
        return $this->p->load($params)->one();
    }

    public function addPrintJob($params)
    {
        return $this->p->save($params);
    }

    public function updatePrintJob($params)
    {
        return $this->p->save($params);
    }

    public function destroyPrintJob($params)
    {
        return $this->p->destroy($params);
    }

}
