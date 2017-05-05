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
 * Enable the error and also set the ROOT directory for
 * the error log. But checks if the files exists and is
 * writable.
 *
 * NOTE: This should be part of Matcha::Connect
 */

/**
 * Check for the script management runner. Quit if it is a
 * web server is detected.
 */
if(php_sapi_name() != 'cli'){
    echo "This script should be ran from the CLI (Command Line Interface)</br>";
    echo "and not from a web server of any kind.</br>";
    exit(0);
}

class CronBootstrap
{
    /**
     * @var bool|MatchaCUP
     */
    private $CronJobModel;

    /**
     * @var array|mixed|object
     */
    private $CronJobParams;

    /**
     * CronBootstrap constructor.
     * Load up all the tools needed to work with the database (current site)
     * and set up all the environment.
     * NOTE: Remember that this is called fromm the CLI (Command Line Interface)
     * and not from Apache or IIS, it will not have $_SESSION, $_SERVER, ect.
     */
    function __construct($argv){
        define("PID",getmypid());
        define('site_id', $argv[1]);
        define('URL', '');
        define('ROOT', str_replace('\\', '/', str_ireplace("cronjob","",getcwd())));
        define('SCRIPT', basename(__FILE__, ".php"));
        define('SCRIPT_NAME', "Old Log Removal");
        include_once(ROOT."sites/".site_id."/conf.php");
        include_once(ROOT.'classes/MatchaHelper.php');

        error_reporting(-1);
        ini_set('display_errors', 1);
        $logPath = ROOT . 'sites/' . site_id . '/log/';
        if(file_exists($logPath) && is_writable($logPath)){
            $logFile = 'error_log.txt';
            $oldUmask = umask(0);
            clearstatcache();
            if(!file_exists($logPath . $logFile)){
                touch($logPath . $logFile);
                chmod($logPath . $logFile, 0764);
            }
            if(is_writable($logPath . $logFile))
                ini_set('error_log', $logPath . $logFile);
            umask($oldUmask);
        }

        if($this->CronJobModel == NULL)
            $this->CronJobModel = \MatchaModel::setSenchaModel('App.model.administration.CronJob');
        $this->CronJobParams = $this->checkCronJobVisibility();
    }

    /**
     * checkRun
     *
     * Check if the script is ok to run, return true if it is time to run,
     * false if not ok to run
     * @param $CronJobParams
     * @return bool
     */
    public function checkRun(){
        $CJP = $this->CronJobParams['data'];

        // This means that the process is already running
        if($this->checkProcessId($this->CronJobParams['data']['pid'])) return false;

        // Check if it's there anything to run first.
        if(isset($this->CronJobParams['data'])) return false;

        // Try to check the time of it's schedule but first check if the script
        // is running, if it is running skip it.
        if($CJP['active'] && !$CJP['running']) {
            // Check month
            if ($CJP['month'] == date('n') || $CJP['month'] == '*') {
                // Check month day
                if ($CJP['month_day'] == date('j') || $CJP['month_day'] == '*') {
                    // Check week day
                    if ($CJP['week_day'] == date('w') || $CJP['week_day'] == '*') {
                        // Check week day
                        if ($CJP['hour'] == date('G') || $CJP['hour'] ==  '*') {
                            // Check minute
                            if ($CJP['minute'] == date('i') || $CJP['hour'] == '*') {
                                $params = new stdClass();
                                $params->filter[0] = new stdClass();
                                $params->filter[0]->property = 'filename';
                                $params->filter[0]->value = SCRIPT;
                                $CronJobRecords = $this->CronJobModel->load($params)->one();

                                $data = new stdClass();
                                $data->id = $CronJobRecords['id'];
                                $data->pid = PID;
                                $data->running = true;
                                $data->last_run_date = date('Y-m-d H:i:s');
                                $this->CronJobModel->save($data);
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * checkProcessId
     *
     * Depending on the Operating system, the method tries to collect all the running processes
     * and compares the PID (Process ID) of the task and the one in the database.
     * @param $PID
     * @return bool
     */
    private function checkProcessId($PID){
        switch(PHP_OS){
            // Windows OS
            case 'WINNT':
                $cmd = "tasklist /FO CSV";
                $result = shell_exec($cmd);
                $header = null;
                $tasks = self::csv_to_array($result, "\n");
                foreach($tasks as $task){
                    if($task['PID'] == $PID) return true;
                }
                break;
            case 'Linux':
                $cmd = 'ps -e -o \"\\" % p\\",\\" % r\\",\\" % U\\",\\" % z\\",\\" % C\\",\\" % c\\",\\" % a\\"\"';
                $result = shell_exec($cmd);
                $header = null;
                $tasks = self::csv_to_array($result, "\n", ",");
                foreach($tasks as $key => $task) $tasks[$key] = trim($task);
                foreach($tasks as $task){
                    if($task['PID'] == $PID) return true;
                }
                break;
            case 'Darwin':
                $cmd = 'ps -e -o \"\\" % p\\",\\" % r\\",\\" % U\\",\\" % z\\",\\" % C\\",\\" % c\\",\\" % a\\"\"';
                $result = shell_exec($cmd);
                $header = null;
                $tasks = self::csv_to_array($result, "\n", ",");
                foreach($tasks as $key => $task) $tasks[$key] = trim($task);
                foreach($tasks as $task){
                    if($task['PID'] == $PID) return true;
                }
                break;
        }
        return false;
    }

    /**
     * checkCronJobVisibility
     *
     * Check if the script is in the database and if not add it to the the
     * database and save the default values in it.
     *
     * @return array|mixed|object
     */
    private function checkCronJobVisibility(){
        // Fetch the script schedule
        $params = new stdClass();
        $params->filter[0] = new stdClass();
        $params->filter[0]->property = 'filename';
        $params->filter[0]->value = SCRIPT;
        $CronJobRecords = $this->CronJobModel->load($params)->one();

        // Save the new cronjob script into the database
        if(empty($CronJobRecords['data'])){
            $data = new stdClass();
            $data->filename = SCRIPT;
            $data->name = SCRIPT_NAME;
            $data->minute = '*';
            $data->hour = '*';
            $data->month_day = '*';
            $data->month = '*';
            $data->week_day = '*';
            $data->timeout = '3600';
            return $this->CronJobModel->save($data);
        } else {
            return $CronJobRecords;
        }
    }

    /**
     * csv_to_array
     * Converts a csv string into an array
     * Original code from: https://gist.github.com/jaywilliams/385876
     * Modification by: http://php.net/manual/en/function.str-getcsv.php#117366
     *
     * @param string $string
     * @param string $row_delimiter
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return array
     */
    private function csv_to_array($string='', $row_delimiter=PHP_EOL, $delimiter = "," , $enclosure = '"' , $escape = "\\" )
    {
        $rows = array_filter(explode($row_delimiter, $string));
        $header = NULL;
        $data = array();
        foreach($rows as $row)
        {
            $row = str_getcsv ($row, $delimiter, $enclosure , $escape);
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        return $data;
    }
}
