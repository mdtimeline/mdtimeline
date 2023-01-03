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

// dynamic class and methods loading test

include_once ('../session.php');

define('_GaiaEXEC', 1);

if(!isset($_SESSION['install']) || (isset($_SESSION['install']) && $_SESSION['install'] != true)){
	require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/registry.php');
	require_once(ROOT . '/sites/' . SITE . '/conf.php');
	require_once(ROOT . '/classes/MatchaHelper.php');
	include_once(ROOT . '/dataProvider/Modules.php');
	$m = new Modules();
}

/*
 * getREMOTING_API
 */
function getREMOTING_API($API, $moduleDir = false) {
	global $site;
	$actions = [];
	foreach($API as $aname => &$a){
		$methods = [];
		foreach($a['methods'] as $mname => &$m){
			if(isset($m['len'])){
				$md = [
					'name' => $mname,
					'len' => $m['len']
				];
			} else {
				$md = [
					'name' => $mname,
					'params' => $m['params']
				];
			}
			if(isset($m['formHandler']) && $m['formHandler'])
				$md['formHandler'] = true;
			$methods[] = $md;
		}
		$actions[$aname] = $methods;
	}
	$url = ($moduleDir === false ? "data/router.php?site={$site}" : "data/router.php?site={$site}&module={$moduleDir}");
	return json_encode([
		'url' => $url,
		'type' => 'remoting',
		'actions' => $actions,
		'timeout' => 760000000
	]);
}

require('config.php');
// convert API config to Ext.Direct spec
header('Content-Type: text/javascript');
echo 'Ext.ns("App.data");';
echo 'App.data = [];';
echo 'App.data.push(' . getREMOTING_API($API) . ');';
if(isset($_SESSION['install']) && $_SESSION['install'] != true){
	foreach($m->getEnabledModules() AS $module){
		echo 'App.data.push(' . getREMOTING_API($module['actionsAPI'], $module['dir']) . ');';
	}
}

