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
class Globals {

	/**
	 * @var bool|MatchaCUP
	 */
	private static $g = null;

    /**
     * Apply the pre-defined validators items into the saving event global item
     *
     * @param $params
     * @return mixed
     */
    public static function applyValidations($params){
        if(self::$g == null)
            self::$g = MatchaModel::setSenchaModel('App.model.administration.Globals');
        $global = self::$g->load(['id' => $params->id])->one();
        if(isset($global['gl_name'])) {
            switch ($global['gl_type']) {
                case 'truefalse':
                    if ($params->gl_value == 'true' || $params->gl_value == 1) {
                        $params->gl_value = 'true';
                    } else {
                        $params->gl_value = 'false';
                    }
                    return $params;
                    break;
                case 'yesno':
                    if ($params->gl_value == 'yes') {
                        $params->gl_value = 'yes';
                    } else {
                        $params->gl_value = 'no';
                    }
                    return $params;
                    break;
                case 'host':
                    $pattern = '/^(http:\/\/|https:\/\/|ftp:\/\/)/';
                    $params->gl_value = preg_replace($pattern, '', $params->gl_value);
                    return $params;
                    break;
                case 'text':
                    return $params;
                    break;
                case 'dir':
                    if(strpos($params->gl_value, '/') === false){
                        $params->gl_value = '';
                        return $params;
                    } else {
                        return $params;
                    }
                    break;
                case 'numeric':
                    if(!is_numeric($params->gl_value)) $params->gl_value = '';
                    return $params;
                    break;
                default:
                    return $params;
                    break;
            }
        }
        return $params;
    }

	/**
	 * @return array
	 */
	public static function getGlobals() {
		if(self::$g == null)
			self::$g = MatchaModel::setSenchaModel('App.model.administration.Globals');
		return self::$g->load()->all();
	}

	/**
	 * @param stdClass $params
	 * @return stdClass
	 */
	public function updateGlobals($params) {
		if(self::$g == null)
			self::$g = MatchaModel::setSenchaModel('App.model.administration.Globals');
		$params = self::$g->save(self::applyValidations($params));
		$this->setGlobals();
		return $params;
	}

	/**
	 * @static
	 * @return mixed
	 */
	public static function setGlobals() {
        if(!isset($_SESSION['globals']))
            $_SESSION['globals'] = array();

		if(self::$g == null)
			self::$g = MatchaModel::setSenchaModel('App.model.administration.Globals');
		foreach(self::$g->load()->all() as $setting){
		    if($setting['gl_type'] == 'truefalse')
		        $setting['gl_value'] = self::convert($setting['gl_value']);
			$_SESSION['globals'][$setting['gl_name']] = $setting['gl_value'];
		}
		$_SESSION['globals']['timezone_offset'] = -14400;
		$_SESSION['globals']['date_time_display_format'] = $_SESSION['globals']['date_display_format'] . ' ' . $_SESSION['globals']['time_display_format'];
		return $_SESSION['globals'];
	}

	/**
	 * @return array
	 */
	public static function getGlobalsArray() {
		if(self::$g == null)
			self::$g = MatchaModel::setSenchaModel('App.model.administration.Globals');
		$gs = array();
		foreach(self::$g->load()->all() AS $g){
			$gs[$g['gl_name']] = $g['gl_value'];
		}
		return $gs;
	}

	/**
	 * @param string $global
	 * @return mix
	 */
	public static function getGlobal($global) {
		if(!isset($_SESSION['globals'])){
			self::setGlobals();
			return self::getGlobal($global);
		} else {
			return isset($_SESSION['globals'][$global]) ? $_SESSION['globals'][$global] : false;
		}
	}

    /**
     * Simple internal function to convert a true or false string based to a true
     * bool result.
     * @param $value
     * @return bool
     */
	private static function convert($value){
	    if($value == 'false'){
	        return false;
        }elseif($value == 'true'){
            return true;
        }
        return $value;
    }

}
