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
include_once (dirname(__FILE__).'/Segments.php');

class QAK extends Segments{

	function __destruct(){
		parent::__destruct();
	}

	function __construct($hl7){
		parent::__construct($hl7, 'QAK');

		$this->setField(1, 'ST', 4);
        /**
         * OK	Data found, no errors (this is the default)
         * NF	No data found, no errors
         * AE	Application error
         * AR	Application reject
         * PD   Protected data
         * TM   Too much data found
         */
		$this->setField(2, 'ID', 32);

		$this->setField(3, 'CE', 250, true, true);
		$this->setField(4, 'NM', 250, false, true);
		$this->setField(5, 'NM', 250, true, true);
		$this->setField(6, 'NM', 4, false, true);


	}
}