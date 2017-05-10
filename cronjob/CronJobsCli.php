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

$env_dir = str_replace('CronJobsCli.php', '', $_SERVER['PHP_SELF']);

/**
 * Load the complete list of sites directory into a variable (array) also
 * removes unwanted files and dotted directories
 */
$directories = array_diff(scandir($sites_dir), array('..', '.'));
foreach($directories as $index => $directory)
    if(!is_dir($sites_dir.$directory)) unset($directories[$index]);
$directories = array_values($directories);

/**
 * Check for a conf.php file and also a jobs directory, if the below code founds
 * a jobs php script, try to run it, and then finish the loop with an exit until
 * next call from the CronJob Service (Linux or Mac) or Task Scheduler (Windows)
 */
foreach($directories as $directory){
    $conf = $sites_dir . $directory . '/conf.php';
    if(file_exists($conf) && is_dir($sites_dir . $directory . "/jobs")){

        // Fetch all the JOBS available on the site
        $jobsFiles = array_diff(scandir($sites_dir . $directory . "/jobs/"), array('..', '.'));

        // Loop on all the jobs available and execute them
        foreach($jobsFiles as $jobsFile){
            $env = 'cd '.$env_dir.' && ';
            $cmd = $env." php -f ".$sites_dir.$directory ."/jobs/".$jobsFile." ".$directory." &";
            shell_exec($cmd);
            print "Executing: $jobsFile...\n";
        }
    }
}
print "All scripts where executed..\n";
exit(0);
