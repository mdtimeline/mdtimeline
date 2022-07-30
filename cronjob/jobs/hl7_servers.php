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

/**
 * BootStrap file
 */
include_once("CronBootstrap.php");
$CronBootstrap = new \CronBootstrap($argv, basename(__FILE__, ".php"));
if(!$CronBootstrap->checkRun()) exit(0);

/**
 * Task
 */

include_once (ROOT . '/dataProvider/HL7ServerHandler.php');
$BackUp = new HL7ServerHandler();
$BackUp->check();

$CronBootstrap->end();
