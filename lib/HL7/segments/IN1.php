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

class IN1 extends Segments{

	function __destruct(){
		parent::__destruct();
	}

	function __construct($hl7){
		parent::__construct($hl7, 'IN1');
		$this->setField(1, 'SI', 4, true);
		$this->setField(2, 'CE', 250, true);
		$this->setField(3, 'CX', 250, true, true);
		$this->setField(4, 'XON', 250, false, true);
		$this->setField(5, 'XAD', 250, false, true);
		$this->setField(6, 'XPN', 250, false, true);
		$this->setField(7, 'XTN', 250, false, true);
		$this->setField(8, 'ST', 12);
		$this->setField(9, 'XON', 250, false, true);
		$this->setField(10, 'CX', 250, false, true);
		$this->setField(11, 'XON', 250, false, true);
		$this->setField(12, 'DT', 8);
		$this->setField(13, 'DT', 8);
		$this->setField(14, 'AUI', 239);
		$this->setField(15, 'IS', 3);
		$this->setField(16, 'XPN', 250, false, true);
		$this->setField(17, 'CE', 250);
		$this->setField(18, 'TS', 26);
		$this->setField(19, 'XAD', 250, false, true);
		$this->setField(20, 'IS', 2);
		$this->setField(21, 'IS', 2);
		$this->setField(22, 'ST', 2);
		$this->setField(23, 'ID', 1);
		$this->setField(24, 'DT', 8);
		$this->setField(25, 'ID', 1);
		$this->setField(26, 'DT', 8);
		$this->setField(27, 'IS', 2);
		$this->setField(28, 'ST', 15);
		$this->setField(29, 'TS', 26);
		$this->setField(30, 'XCN', 250, false, true);
		$this->setField(31, 'IS', 2);
		$this->setField(32, 'IS', 2);
		$this->setField(33, 'NM', 4);
		$this->setField(34, 'NM', 4);
		$this->setField(35, 'IS', 8);
		$this->setField(36, 'ST', 15);
		$this->setField(37, 'CP', 12);
		$this->setField(38, 'CP', 12);
		$this->setField(39, 'NM', 4);
		$this->setField(40, 'CP', 12);
		$this->setField(41, 'CP', 12);
		$this->setField(42, 'CE', 250);
		$this->setField(43, 'IS', 1);
		$this->setField(44, 'XAD', 250, false, true);
		$this->setField(45, 'ST', 2);
		$this->setField(46, 'IS', 8);
		$this->setField(47, 'IS', 3);
		$this->setField(48, 'IS', 2);
		$this->setField(49, 'CX', 250, false, true);
		$this->setField(50, 'IS', 1);
		$this->setField(51, 'DT', 8);
		$this->setField(52, 'ST', 250);
		$this->setField(53, 'IS', 2);
	}
}