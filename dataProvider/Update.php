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

class Update {


    function __construct() {

    }

    public function getModules($params) {

        $modules = ['core', 'worklist','cqmsolution'];

        include_once(ROOT . '/dataProvider/Modules.php');
        include_once(ROOT . '/dataProvider/Gitter.php');
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

}
