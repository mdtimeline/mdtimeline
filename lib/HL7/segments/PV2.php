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

class PV2 extends Segments{

	function __destruct(){
		parent::__destruct();
	}

	function __construct($hl7){
		parent::__construct($hl7);

        parent::__construct($hl7, 'PV2');
        $this->setField(1, 'PL', 1);
        $this->setField(2, 'CE', 1);
        $this->setField(3, 'CE', 1);
        $this->setField(4, 'CE', 1);
        $this->setField(5, 'ST', 1);
        $this->setField(6, 'ST', 1);
        $this->setField(7, 'IS', 1);
        $this->setField(8, 'TS', 1);
        $this->setField(9, 'TS', 1);
        $this->setField(10, 'NM', 1);
        $this->setField(11, 'NM', 1);
        $this->setField(12, 'ST', 1);
        $this->setField(13, 'XCN', 1);
        $this->setField(14, 'DT', 1);
        $this->setField(15, 'ID', 1);
        $this->setField(16, 'IS', 1);
        $this->setField(17, 'DT', 1);
        $this->setField(18, 'IS', 1);
        $this->setField(19, 'ID', 1);
        $this->setField(20, 'NM', 1);
        $this->setField(21, 'IS', 1);
        $this->setField(22, 'ID', 1);
        $this->setField(23, 'XON', 1);
        $this->setField(24, 'IS', 1);
        $this->setField(25, 'IS', 1);
        $this->setField(26, 'DT', 1);
        $this->setField(27, 'IS', 1);
        $this->setField(28, 'DT', 1);
        $this->setField(29, 'DT', 1);
        $this->setField(30, 'CE', 1);
        $this->setField(31, 'IS', 1);
        $this->setField(32, 'ID', 1);
        $this->setField(33, 'TS', 1);
        $this->setField(34, 'ID', 1);
        $this->setField(35, 'ID', 1);
        $this->setField(36, 'ID', 1);
        $this->setField(37, 'ID', 1);
        $this->setField(38, 'CE', 1);
        $this->setField(39, 'CE', 1);
        $this->setField(40, 'CE', 1);
        $this->setField(41, 'CE', 1);
        $this->setField(42, 'CE', 1);
        $this->setField(43, 'IS', 1);
        $this->setField(44, 'IS', 1);
        $this->setField(45, 'CE', 1);
        $this->setField(46, 'DT', 1);
        $this->setField(47, 'TS', 1);
        $this->setField(48, 'TS', 1);
        $this->setField(49, 'IS', 1);

	}
}