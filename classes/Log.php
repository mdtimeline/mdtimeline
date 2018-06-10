<?php
/**
GaiaEHR (Electronic Health Records)
Copyright (C) 2013 Certun, LLC.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Log
{
	static public function init(){
		error_reporting(-1);
		ini_set('display_errors', 1);
		$log_path = ROOT . '/sites/' . SITE . '/log/';
		$log_file = 'error.log';
		$filename = $log_path . $log_file;

		if(!file_exists($filename)){
			touch($filename);
			chmod($filename, 0764);
		}


		$old_umask = umask(0);
		clearstatcache();
		$filename = $log_path . $log_file;

		if(is_writable($filename)) {
			ini_set('error_log', $filename);
		}

		umask($old_umask);

	}

	static public function rotate($filename = null){

		if(!isset($filename)){
			error_reporting(-1);
			ini_set('display_errors', 1);
			$log_path = ROOT . '/sites/' . site_id . '/log/';
			$log_file = 'error.log';
			$filename = $log_path . $log_file;
		}

		$keep = 10;

		error_log('####'. $filename);

		if (file_exists($filename)) {
			//if (date ('Y-m-d', filemtime($filename)) !== date('Y-m-d')) {
				if (file_exists($filename . "." . $keep)) {
					unlink($filename . "." . $keep);
				}
				for ($i = $keep; $i > 0; $i--) {
					if (file_exists($filename . "." . $i)) {
						$next = $i+1;
						rename($filename . "." . $i, $filename . "." . $next);
					}
				}
				rename($filename, $filename . ".1");
			//}
		}
	}

	static private function rename($filename){
		rename($filename, $filename .'.old');
	}




}
