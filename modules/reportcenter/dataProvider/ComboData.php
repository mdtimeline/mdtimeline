<?php
/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
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

class CombosData
{
    /**
     * getTableList
     * Used by TransactionLog report, this will return a list of distinct tables in the transaction log.
     */
    public function getTableList()
    {
        $this->TransactionLog = MatchaModel::setSenchaModel('App.model.administration.TransactionLog');
        $sql = "SELECT distinct(table_name) as table_name FROM audit_transaction_log;";
        return $this->TransactionLog->sql($sql)->all();
    }
}
