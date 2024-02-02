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
                    'tags' => []
                ];
            } else {
                $log = $Gitter->doLog($module);
                $branch = $Gitter->doBranch($module);
                $branches = $Gitter->doBranches($module);
                $tag = $Gitter->doGetCurrentTag($module);
                $branchesArray = [];
                $tags = $Gitter->doTags($module);
                $tagsArray = [];

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
                    }
                } else {
                    $branch = '';
                }

                if (isset($tag['output'])) {
                    foreach ($tag['output'] as &$output){
                        $tag = $output;
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
        $updateScripts = $this->getDatabaseUpdateScripts($selectedUpdateScripts[0]->module);
        $conn = Matcha::getConn();

        foreach($selectedUpdateScripts as $selectedUpdateScript) {
            try {
                foreach($updateScripts as $updateScript) {

                    // found script to be executed
                    if (version_compare($selectedUpdateScript->version, $updateScript->version) == 0) {
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
        }

        return $result;
    }

    public function doGitUpdate($module) {
        $Gitter = new Gitter();

        $gitResult = $Gitter->doPull($module);
        $databaseUpdateScriptsResult = $this->getDatabaseUpdateScripts($module);
        $gitResult['databaseUpdateScripts'] = $databaseUpdateScriptsResult;

        return $gitResult;
    }

    private function getDatabaseUpdateScripts($module) {
        $Version = new Version();
        $databaseUpdateFiles = [];

        $updateFilesPath = ROOT . "/modules/{$module}/resources/sql/updates";
        $currentVersion = $Version->getModuleLatestUpdate($module);
        $updateFiles = array_diff(scandir($updateFilesPath), array('.', '..'));
        natsort($updateFiles);

        // Find the needed update file
        foreach ($updateFiles as $file) {
            $result = preg_match_all("/^update-([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})/",
                $file,
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
                            file_get_contents(ROOT . "/modules/{$module}/resources/sql/updates/" . $file),
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
                                    $databaseUpdateFiles[] = $updateFile;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $databaseUpdateFiles;
    }
}
