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
include_once(ROOT . '/dataProvider/Modules.php');
include_once(ROOT . '/dataProvider/Gitter.php');
include_once(ROOT . '/dataProvider/Version.php');


class Update {


    function __construct() {

    }

    public function getModules($params) {

        $modules = ['core', 'worklist','cqmsolution', 'billing'];
        $Modules = new Modules();
        $Gitter = new Gitter();
        $Version = new Version();

        $installed_modules = $Modules->getAllModules();

        $data = [];
        foreach ($modules as $module){

            /*
             * core  = VERSION
             * module = config
             */

            // Check if directory exists for the current module
            $directory = ROOT . "/modules/{$module}";
            if($module != 'core' && !file_exists($directory)){
                $data[] = [
                    'module' => $module,
                    'version' => VERSION, // config....
                    'script_version' => 'MODULE NOT INSTALLED',
                    'current_branch' => 'MODULE NOT INSTALLED',
                    'current_tag' => 'MODULE NOT INSTALLED',
                    'latest_commit' => 'MODULE NOT INSTALLED',
                    'branches' => [],
                    'tags' => [],
                    'information' => 'MODULE NOT INSTALLED'
                ];
            } else {
                $information = [];
                $log = $Gitter->doLog($module);
                $branch = $Gitter->doBranch($module);
                $branches = $Gitter->doBranches($module);
                $tag = $Gitter->doGetCurrentTag($module);
                $branchesArray = [];
                $tags = $Gitter->doTags($module);
                $currentDatabaseVersion = $Version->getModuleLatestUpdate($module);
                // $moduleVersion = $Modules->getModuleByName($module);
                $tagsArray = [];

                // Get the database version
                if ($currentDatabaseVersion) {
                    $information[] = "Database Version: {$currentDatabaseVersion['full_version']}";
                } else {
                    $information[] = "Database Version: NOT DEFINED";
                }

                // Check if branch was found, or use tag name
                if (!isset($branch['output'][0])) {
                    $branch = '';
                }

                if (!isset($tag['output'][0])) {
                    $tag = '';
                }

                foreach ($log['output'] as &$output){
                    $output = htmlspecialchars($output);
                }

                if (isset($branch['output'])) {
                    foreach ($branch['output'] as &$output){
                        $branch = $output;
                        $information[] = "Current Branch: {$branch}";
                    }
                } else {
                    $branch = '';
                }

                if (isset($tag['output'])) {
                    foreach ($tag['output'] as &$output){
                        $tag = $output;
                        $information[] = "Current Tag: {$tag}";
                    }
                } else {
                    $tag = '';
                }

                foreach ($branches as &$output){
                    $object = new stdClass();
                    $object->text = $output;
                    $object->iconCls = 'fas fa-code-branch';
                    $object->module = $module;

                    $branchesArray[] = $object;
                }

                foreach ($tags['output'] as &$output){
                    $object = new stdClass();
                    $object->text = $output;
                    $object->iconCls = 'fas fa-tag';
                    $object->module = $module;

                    $tagsArray[] = $object;
                }

                $data[] = [
                    'module' => $module,
                    'version' => VERSION, // config....
                    'information' => implode('<br>', $information),
                    'script_version' => 'v2.3',
                    'current_branch' => $branch,
                    'current_tag' => $tag,
                    'latest_commit' => implode('<br>', $log['output']),
                    'branches' => $branchesArray,
                    'tags' => $tagsArray
                ];
            }
        }

        return $data;
    }

    public function doDatabaseUpdateScripts($params) {
        $result = [
            'success' => [],
            'error' => []
        ];

        $selectedUpdateScripts = $params;
        $updateScripts = $this->getAllDatabaseUpdateScripts($selectedUpdateScripts[0]->module);
        $conn = Matcha::getConn();
        foreach($selectedUpdateScripts as $selectedUpdateScript) {
            $foundScript = false;

            try {
                foreach($updateScripts as $updateScript) {
                    // found script to be executed
                    if (version_compare($selectedUpdateScript->version, $updateScript->version) == 0) {
                        if ((!isset($updateScript->script)) || ($updateScript->script == ''))
                            throw new Exception("The selected update script version ({$updateScript->version}) was not found in the update files!");

                        $foundScript = true;
                        $sth = $conn->prepare($updateScript->script);
                        $sth = $sth->execute();

                        // Executed script successfully, call setVersion
                        if ($sth) {
                            $version = explode('.', $updateScript->version);

                            $sth = $conn->prepare("CALL setVersion({$version[0]}, {$version[1]}, {$version[2]}, '{$updateScript->module}');");
                            $sth = $sth->execute();

                            // Call setVersion executed successfully
                            if ($sth) {
                                array_push($result['success'], "Version ({$updateScript->version}): " . $updateScript->script);
                            }
                        }
                    }
                }
            } catch(Exception $e) {
                array_push($result['error'], "Version ({$updateScript->version}): " . $e->getMessage());
                break;
            }

            if (!$foundScript)
                array_push($result['error'], "The selected update script version ({$selectedUpdateScript->version}) was not found in the update files!");
        }

        return $result;
    }

    public function doGitUpdate($module) {
        $Gitter = new Gitter();

        $gitResult = $Gitter->doPull($module);
        $databaseUpdateScriptsResult = $this->getDatabasePendingUpdateScripts($module);
        $gitResult['databaseUpdateScripts'] = $databaseUpdateScriptsResult;

        return $gitResult;
    }

    public function doGetDatabaseUpdateScripts($module) {
        $Version = new Version();
        $returnResult = [];

        $databaseUpdateScriptsInDirectory = $this->getDatabaseUpdateScripts($module);
        $databasePendingUpdateScriptsInDirectory = $this->getDatabasePendingUpdateScripts($module);
        $databaseVersionRecords = $Version->getAllModuleUpdates($module);

        // Get an array of the field values to sort by
        $fieldName = array_column($databasePendingUpdateScriptsInDirectory, 'version');

        // Sort the array of objects using array_multisort()
        array_multisort($fieldName, SORT_ASC, $databasePendingUpdateScriptsInDirectory);

        // Get all applied scripts in database
        foreach($databaseVersionRecords as $databaseVersionRecord) {
            $record = new stdClass();
            $record->module = $module;
            $record->version = $databaseVersionRecord['full_version'];
            $record->timestamp = $databaseVersionRecord['v_timestamp'];
            $record->script = '';

            foreach($databaseUpdateScriptsInDirectory as $databaseUpdateScriptInDirectory) {
                $result = preg_match_all("/^update-([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})/",
                    $databaseUpdateScriptInDirectory,
                    $keys,
                    PREG_PATTERN_ORDER);

                // if matched
                if ($result > 0) {
                    // $keys[0] = FILE NAME
                    // $keys[1] = MAJOR VERSION
                    // $keys[2] = MINOR VERSION
                    // $keys[3] = PATCH VERSION
                    $fileMajorVersion = (int)$keys[1][0];
                    $fileMinorVersion = (int)$keys[2][0];
                    $filePatchVersion = (int)$keys[3][0];

                    $updateFileResult = preg_match_all("/-- \[([\d.]*)] --(.*?)CALL setVersion\(\d*, \d*, \d*, '(\w*)'\)/ms",
                        file_get_contents(ROOT . "/modules/{$module}/resources/sql/updates/" . $databaseUpdateScriptInDirectory),
                        $scripts,
                        PREG_SET_ORDER);

                    // Compare by patch version
                    if ($updateFileResult > 0) {
                        foreach ($scripts as $script) {
                            if (version_compare($script[1], $record->version) == 0) {
                                $record->script = $script[2];
                                break;
                            }
                        }
                    }
                }

                $returnResult[] = $record;
            }
        }

        // Get pending update scripts first...
        foreach($databasePendingUpdateScriptsInDirectory as $databasePendingUpdateScriptInDirectory) {
            $record = new stdClass();
            $record->module = $module;
            $record->version = $databasePendingUpdateScriptInDirectory->version;
            $record->script = $databasePendingUpdateScriptInDirectory->script;
            $record->timestamp = '';

            $returnResult[] = $record;
        }

        return $returnResult;
    }

    private function getDatabasePendingUpdateScripts($module) {
        $Version = new Version();
        $pendingScripts = $this->getDatabaseUpdateScripts($module);
        $currentVersion = $Version->getModuleLatestUpdate($module);
        $pendingScriptsResult = [];

        foreach($pendingScripts as $pendingScript) {
            $result = preg_match_all("/^update-([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})/",
                $pendingScript,
                $keys,
                PREG_PATTERN_ORDER);

            // if matched
            if ($result > 0) {
                // $keys[0] = FILE NAME
                // $keys[1] = MAJOR VERSION
                // $keys[2] = MINOR VERSION
                // $keys[3] = PATCH VERSION
                $fileMajorVersion = (int)$keys[1][0];
                $fileMinorVersion = (int)$keys[2][0];
                $filePatchVersion = (int)$keys[3][0];

                $currentMajorVersion = $currentVersion['v_major'];
                $currentMinorVersion = $currentVersion['v_minor'];
                $currentPatchVersion = $currentVersion['v_patch'];

                $currentDatabaseVersion = false;
                if ($currentMajorVersion == $fileMajorVersion)
                    $currentDatabaseVersion = true;

                if ($currentMajorVersion <= $fileMajorVersion) {
                    if ((!$currentDatabaseVersion) || ($currentMinorVersion <= $fileMinorVersion)) {
                        // Read the file and check if scripts need to be executed...
                        $updateFileResult = preg_match_all("/-- \[([\d.]*)] --(.*?)CALL setVersion\(\d*, \d*, \d*, '(\w*)'\)/ms",
                            file_get_contents(ROOT . "/modules/{$module}/resources/sql/updates/" . $pendingScript),
                            $scripts,
                            PREG_SET_ORDER);

                        // Compare by patch version
                        if ($updateFileResult > 0) {
                            foreach ($scripts as $script) {
                                if (version_compare($script[1], $currentVersion['full_version']) > 0) {
                                    // prepare array to display scripts that need to be executed...
                                    $updateFile = new stdClass();
                                    $updateFile->module = $module;
                                    $updateFile->version = $script[1];
                                    $updateFile->script = $script[2];
                                    $pendingScriptsResult[] = $updateFile;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $pendingScriptsResult;
    }

    private function getAllDatabaseUpdateScripts($module) {
        $Version = new Version();
        $updateScripts = $this->getDatabaseUpdateScripts($module);
        $currentVersion = $Version->getModuleLatestUpdate($module);
        $allUpdateScriptsResult = [];

        foreach($updateScripts as $updateScript) {
            $result = preg_match_all("/^update-([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})/",
                $updateScript,
                $keys,
                PREG_PATTERN_ORDER);

            // if matched
            if ($result > 0) {
                // $keys[0] = FILE NAME
                // $keys[1] = MAJOR VERSION
                // $keys[2] = MINOR VERSION
                // $keys[3] = PATCH VERSION
                $fileMajorVersion = (int)$keys[1][0];
                $fileMinorVersion = (int)$keys[2][0];
                $filePatchVersion = (int)$keys[3][0];

                $currentMajorVersion = $currentVersion['v_major'];
                $currentMinorVersion = $currentVersion['v_minor'];
                $currentPatchVersion = $currentVersion['v_patch'];


                // Read the file and check if scripts need to be executed...
                $updateFileResult = preg_match_all("/-- \[([\d.]*)] --(.*?)CALL setVersion\(\d*, \d*, \d*, '(\w*)'\)/ms",
                    file_get_contents(ROOT . "/modules/{$module}/resources/sql/updates/" . $updateScript),
                    $scripts,
                    PREG_SET_ORDER);

                // Compare by patch version
                if ($updateFileResult > 0) {
                    foreach ($scripts as $script) {
                        // prepare array to display scripts that need to be executed...
                        $updateFile = new stdClass();
                        $updateFile->module = $module;
                        $updateFile->version = $script[1];
                        $updateFile->script = $script[2];
                        $allUpdateScriptsResult[] = $updateFile;
                    }
                }
            }
        }

        return $allUpdateScriptsResult;
    }

    private function getDatabaseUpdateScripts($module) {
        $Version = new Version();
        $databaseUpdateFiles = [];

        if ($module == 'core' || $module == '')
            $updateFilesPath = ROOT . '/sql';
        else
            $updateFilesPath = ROOT . "/modules/{$module}/resources/sql";

        // check if sql folder exists in resources, else create it...
        if(!file_exists($updateFilesPath)){
            mkdir($updateFilesPath, 0755);
            chmod($updateFilesPath,0755);

            // Then create sql updates folder...
            mkdir($updateFilesPath . '/updates', 0755);
            chmod($updateFilesPath . '/updates',0755);
        }

        // Check if sql update folder exists
        $updateFilesPath .= '/updates';
        if(!file_exists($updateFilesPath)){
            mkdir($updateFilesPath, 0755);
            chmod($updateFilesPath,0755);
        }

        $currentVersion = $Version->getModuleLatestUpdate($module);
        $updateFiles = array_diff(scandir($updateFilesPath), array('.', '..'));
        natsort($updateFiles);

        return $updateFiles;
    }
}
