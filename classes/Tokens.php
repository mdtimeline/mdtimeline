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

class Tokens
{
	/**
	 * @param       $string
	 * @param array $additional_tokens
	 *
	 * @return string
	 */
	public static function StringReplace($string, $additional_tokens = []) {
		$now = new DateTime();

		$tokens = [
			'{DATE}',
			'{TIME}',
			'{DATETIME}',
			'{YEAR}',
			'{MONTH}',
			'{DAY}'
		];

		$values = [
			$now->format('Ymd'),
			$now->format('His'),
			$now->format('YmdHis'),
			$now->format('Y'),
			$now->format('m'),
			$now->format('d')
		];

		if(!empty($additional_tokens)){
			$tokens = array_merge($tokens, array_keys($additional_tokens));
			$values = array_merge($values, array_values($additional_tokens));
		}

		$string = str_replace($tokens, $values, $string);

		if(isset($prefix)){
			$string = $prefix . $string;
		}

		if(isset($suffix)){
			$string = $string . $suffix;
		}

		return trim($string);
	}

}
