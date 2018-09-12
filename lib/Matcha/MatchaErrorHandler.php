<?php

/**
 * Matcha::connect
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
class MatchaErrorHandler extends Matcha {

	public $__logFile;

	/**
	 * function __errorProcess($errorException):
	 * Handle the error of the exception generated by Matcha:connect
	 * it now support FirePHP and ChomePHP.
	 * @param $e
	 * @param bool $__scope
	 * @return
	 */
	static public function __errorProcess($e, &$__scope = false) {

		// construct the exception error
		$trace = $e->getTrace();
		$msg = $e->getMessage();
		$constructErrorMessage = 'Exception: "';
		$constructErrorMessage .= MatchaModel::$fileModel;
		$constructErrorMessage .= ' Message: "';
		$constructErrorMessage .= $msg;
		$constructErrorMessage .= '" ';

		if($__scope){
			$constructErrorMessage .= 'SQL Statement: ' . $__scope->sql . '; ';
		}

		$constructErrorMessage .=  PHP_EOL;

		if(!empty($trace) && is_array($trace)){
			$constructErrorMessage .= ' Trace: ' . PHP_EOL;
			$buff = [];
			foreach ($trace as $t){
				$buff[] = '** file: ' . $t['file'] . ' line: ' . $t['line'] . ' function: ' . $t['function'];
			}
			$constructErrorMessage .= implode(PHP_EOL, $buff);
			unset($buff);
		}


		// normal output - to Apache error.log
		error_log('Matcha::connect: ' . $constructErrorMessage);

		return $e;
	}

	/**
	 * function __errorLogFile:
	 * A file that MatchaErrorHandler will put all the errors
	 * events generated by Matcha::connect
	 */
	static public function __errorLogFile($file = NULL) {
		self::$__logFile = $file;
	}

}
