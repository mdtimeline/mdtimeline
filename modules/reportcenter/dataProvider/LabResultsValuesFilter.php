<?php
/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2015 TRA NextGen, Inc.
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

namespace modules\reportcenter\dataProvider;

class LabResultsValuesFilter
{

    function __construct()
    {
        $this->db = new \MatchaHelper();
        return;
    }

    public function getDistinctResults()
    {
        try
        {
            $this->db->setSQL("SELECT distinct code, code_text FROM patient_order_results");
            $records = $this->db->fetchRecords(\PDO::FETCH_ASSOC);
            return [
                'totals' => count($records),
                'rows' => $records
            ];
        }
        catch(\Exception $Error)
        {
            error_log(print_r($Error, true));
            return $Error;
        }
    }

}
