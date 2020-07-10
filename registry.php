<?php
/**
 * GaiaEHR
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
 * Handle all in UTF-8
 */
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');

if(!defined('_GaiaEXEC'))
	die('No direct access allowed.');

date_default_timezone_set('UTC');

if(!defined('HTTP')){
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)){
		define('HTTP', 'https');
	} else {
		define('HTTP', 'http');
	}
}
if(!defined('HOST'))
	define('HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
if(!defined('URI'))
	define('URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/gaiaehr/');
if(!defined('ROOT'))
	define('ROOT', str_replace('\\', '/', dirname(__FILE__)));
if(!defined('URL')){
	$URL = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : HTTP . '://' . HOST . URI;
	$URL = rtrim(preg_replace('/dataProvider\/.*|modules\/.*/', '', $URL), '/');
	$URL = rtrim(preg_replace('/\?(.*)/', '', $URL), '/');
	define('URL', $URL);
}

// application version
if(!defined('VERSION'))
	define('VERSION', '2.0.1069');
// extjs sdk directory
if(!defined('EXTJS'))
	define('EXTJS', 'extjs-4.2.1');

// IF CLI get out...
if(defined('_ISCLI')) return;


// sites values
$_SESSION['sites'] = [];

if(!defined('sites_count')){
	$sitedir = ROOT . '/sites/';
	$count = 0;
	if($handle = opendir($sitedir)){
		while(false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..' && is_dir($sitedir . $entry) === true)
				$count++;
		}
		closedir($handle);
	}
	define('sites_count', $count);
}

// timeout values
$_SESSION['inactive']['time'] = 30;
$_SESSION['inactive']['start'] = true;
$_SESSION['inactive']['life'] = (time() - (isset($_SESSION['inactive']['timeout']) ? $_SESSION['inactive']['timeout'] : time()));
$_SESSION['inactive']['timeout'] = time();

// cron job
$_SESSION['cron']['delay'] = 60; // in seconds
$_SESSION['cron']['time'] = time(); // store the last cron time stamp

// server data
$_SESSION['server'] = $_SERVER;
$_SESSION['server']['OS'] = (php_uname('s') == 'Linux' ? 'Linux' : 'Windows');
$_SESSION['server']['IS_WINDOWS'] = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$_SESSION['server']['PHP_VERSION'] = phpversion();
$_SESSION['server']['token'] = null;
$_SESSION['server']['last_tid'] = null;

// client data
$_SESSION['client']['browser'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$_SESSION['client']['os'] = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') === false ? 'Linux' : 'Windows');

include_once (ROOT . '/dataProvider/Site.php');

Site::DoUserSiteValidation();

// default site
if(!defined('SITE')){
	$site = Site::GetSite();
	define('SITE', $site);
}else{
	$site = SITE;
}

/**
 * Enable the error and also set the ROOT directory for
 * the error log. But checks if the files exists and is
 * writable.
 *
 * NOTE: This should be part of Matcha::Connect
 */
include_once (ROOT . '/classes/Log.php');
Log::init();

if(file_exists(ROOT . '/sites/' . SITE . '/conf.php')){
	include_once(ROOT . '/sites/' . SITE . '/conf.php');

	if(!defined('site_external_url')){
		define('site_external_url', URL);
	}

	unset($_SESSION['site']['error']);

	include_once(ROOT . '/dataProvider/IpAccessRules.php');
	$IpAccessRules = new IpAccessRules();
	$_SESSION['access_blocked'] = $IpAccessRules->isBlocked();
	//	}

	if($_SESSION['access_blocked']){
		header("HTTP/1.1 401 Unauthorized");
		header('Location: 401.html');
		error_log($IpAccessRules->getUserIP() . ' HTTP/1.1 401 Unauthorized');
		exit;
	}

	// load modules hooks
	if(!isset($_SESSION['hooks'])){
		include_once(ROOT . '/dataProvider/Modules.php');
		$Modules = new Modules();
		$modules = $Modules->getEnabledModules();
		unset($Modules);

		$_SESSION['modules'] = [];
		$_SESSION['styles'] = [];
		$_SESSION['light_styles'] = [];
		$_SESSION['dark_styles'] = [];

		foreach($modules as $module){
			/**
			 * Styles
			 */
			if(isset($module['styles'])){
				foreach($module['styles'] AS $style){
					$css = 'modules/' . $module['name'] . '/resources/css/' . $style;
					if(is_array(isset($_SESSION['styles']) && $_SESSION['styles']) && array_search($css, $_SESSION['styles']) !== false) continue;
					$_SESSION['styles'][] = $css;
				}
			}

			if(isset($module['light_styles'])){
				foreach($module['light_styles'] AS $style){
					$css = 'modules/' . $module['name'] . '/resources/css/' . $style;
					if(is_array(isset($_SESSION['light_styles']) && $_SESSION['light_styles']) && array_search($css, $_SESSION['light_styles']) !== false) continue;
					$_SESSION['light_styles'][] = $css;
				}
			}

			if(isset($module['dark_styles'])){
				foreach($module['dark_styles'] AS $style){
					$css = 'modules/' . $module['name'] . '/resources/css/' . $style;
					if(is_array(isset($_SESSION['dark_styles']) && $_SESSION['dark_styles']) && array_search($css, $_SESSION['dark_styles']) !== false) continue;
					$_SESSION['dark_styles'][] = $css;
				}
			}

			/**
			 * Scripts
			 */
			if(isset($module['scripts'])){
				foreach($module['scripts'] AS $script){
					$js = 'modules/' . $module['name'] . '/resources/js/' . $script;
					if(isset($_SESSION['scripts']) && is_array($_SESSION['scripts']) && array_search($js, $_SESSION['scripts']) !== false) continue;
					$_SESSION['scripts'][] = $js;
				}
			}

			$js = 'modules/' . $module['name'] . '/module.min.js';
			if(file_exists( ROOT . '/' .$js) && array_search($module['name'], $_SESSION['modules']) === false){
				$_SESSION['modules'][] = $module['name'];
			}


			/**
			 * Hooks
			 */
			$HooksFile = ROOT . '/modules/' . $module['name'] . '/dataProvider/Hooks.php';
			if(!file_exists($HooksFile))
				continue;

			include_once($HooksFile);
			$cls = 'modules\\' . $module['name'] . '\dataProvider\Hooks';
			$ReflectionClass = new ReflectionClass($cls);

			$methods = $ReflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

			foreach($methods as $method){
				// if method starts with underscores the skill example "__construct"
				if(preg_match('/^__/', $method->name))
					continue;

				$attributes = explode('_', $method->name);
				if(count($attributes) != 3)
					continue;

				$_SESSION['hooks'][$attributes[1]][$attributes[2]][$attributes[0]]['hooks'][$method->class] = [
					'method' => $method->name,
					'file' => $HooksFile
				];
			}
		}
	}
} else {
	$_SESSION['site'] = ['error' => 'Site configuration file not found, Please contact Support Desk. Thanks!'];
};

