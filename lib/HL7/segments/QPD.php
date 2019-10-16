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

class QPD extends Segments{

	function __destruct(){
		parent::__destruct();
	}

	function __construct($hl7){
		parent::__construct($hl7, 'QPD');

		$this->setField(1, 'CE', 4);
		$this->setField(2, 'ST', 32);

		$this->setField(3, 'CX', 250, true, true);
		$this->setField(4, 'XPN', 250, false, true);
		$this->setField(5, 'XPN', 250, true, true);
		$this->setField(6, 'TS', 4, false, true);
		$this->setField(7, 'IS', 1);
		$this->setField(8, 'XAD', 250, false, true);
		$this->setField(9, 'XTN', 250, false, true);
		/**
		 * PID-10 Race
		 * User-defined Table 0005 - Race
		 * Value Description Comment
		 * 1002-5 American Indian or Alaska Native
		 * 2028-9 Asian
		 * 2054-5 Black or African American
		 * 2076-8 Native Hawaiian or Other Pacific Islander
		 * 2106-3 White
		 * 2131-1 Other Race
		 */
		$this->setField(10, 'ID', 1, false, true);
		$this->setField(11, 'NM', 2, false, true);
		$this->setField(12, 'TS', 4);
		$this->setField(13, 'HD', 250, false, true);


	}
}