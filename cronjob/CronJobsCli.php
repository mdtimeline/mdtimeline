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
 * Check for the script management runner. Quit if it is a
 * web server is detected.
 */
if(php_sapi_name() != 'cli'){
    print "This script should be ran from the CLI (Command Line Interface)</br>";
    print "and not from a web server of any kind.</br>";
    exit(0);
}

date_default_timezone_set('UTC');

switch(PHP_OS) {
    case 'Linux':
        $sites_dir = str_replace('cronjob/CronJobsCli.php', '', $_SERVER['PHP_SELF'])."sites/";
        break;
    case 'Darwin':
        $sites_dir = str_replace('cronjob/CronJobsCli.php', '', $_SERVER['PHP_SELF'])."sites/";
        break;
    case 'WINNT':
        $sites_dir = str_replace('cronjob\CronJobsCli.php', '', $_SERVER['PHP_SELF'])."sites\\";
        break;
}

$env_dir = dirname($_SERVER['PHP_SELF']);
$root_dir = dirname($env_dir);

define('_GaiaEXEC', 1);
define('_ISCLI', 1);

if(file_exists($root_dir . '/registry.php')){
	include_once($root_dir . '/registry.php');
}elseif(file_exists($root_dir . '/../registry.php')){
	include_once ($root_dir . '/../registry.php');
}else{
	error_log('CronJobsCli.php registry file not found. ' . $root_dir . '/registry.php');
}

/**
 * Load the complete list of sites directory into a variable (array) also
 * removes unwanted files and dotted directories
 */
$directories = array_diff(scandir($sites_dir), array('..', '.'));
foreach($directories as $index => $directory)
    if(!is_dir($sites_dir.$directory)) unset($directories[$index]);
$sites = array_values($directories);

/**
 * Check for a conf.php file and also a jobs directory, if the below code founds
 * a jobs php script, try to run it, and then finish the loop with an exit until
 * next call from the CronJob Service (Linux or Mac) or Task Scheduler (Windows)
 */

$php_inis = [
	'/usr/local/etc/php/7.0/php.ini',
	'/etc/php/7.1/apache2/php.ini',
	'/etc/php/7.0/apache2/php.ini',
	'/etc/php/5.6/php.ini',
	'/etc/php5/apache2/php.ini',
	'/etc/php.ini'
];

$php_ini = '';
foreach($php_inis as $file){
	$php_ini = $file;
	if(file_exists($php_ini)) break;
}

$system_jobs = array_diff(scandir("{$root_dir}/cronjob/jobs/"), array('..', '.'));

if(!isset($_SESSION)){
	session_cache_limiter('private');
	session_name('mdTimeLine');
	session_start();
}

foreach($sites as $site){
	$site_dir = $sites_dir . $site;
    $conf = $site_dir . '/conf.php';

	// Loop on all the system jobs available and execute them
	foreach($system_jobs as $job){
		$env = "cd {$env_dir} && ";
		$cmd = "{$env} php  -c {$php_ini} -f {$root_dir}/cronjob/jobs/{$job} {$site} &";
		shell_exec($cmd);
		print "Executing System Job: $job  Site: $site\n";
	}

    if(file_exists($conf) && is_dir($site_dir . "/jobs")){
	    // Fetch all the JOBS available on the site
	    $site_jobs = array_diff(scandir($sites_dir . $site . "/jobs/"), array('..', '.'));

        // Loop on all the sites jobs available and execute them
        foreach($site_jobs as $job){
            $env = "cd {$env_dir} && ";
            $cmd = "{$env} php  -c {$php_ini} -f {$site_dir}/jobs/{$job} {$site} &";
            shell_exec($cmd);
            print "Executing Site Ini: $php_ini - Job: $job -  Site: $site\n";
        }
    }
}
print "All scripts where executed..\n";
exit(0);
