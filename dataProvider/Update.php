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

        $modules = ['core', 'worklist'];

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

            $log = $Gitter->doLog($module);
            $branch = $Gitter->doBranch($module);

            foreach ($log['output'] as &$output){
                $output = htmlspecialchars($output);
            }

            foreach ($branch['output'] as &$output){
                $branch = $output;
            }

            $data[] = [
                'module' => $module,
                'version' => VERSION, // config....
                'script_version' => 'v2.3',
                'current_branch' => $branch,
                'latest_commit' => implode('<br>', $log['output'])
            ];


        }

        return $data;
    }

}
