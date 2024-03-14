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

include_once(ROOT . '/dataProvider/ACL.php');

class SiteConfiguration {

    public function getSiteConfiguration(){
        if(!ACL::hasPermission('access_admin_site_configuration')){
            return 'Unauthorized';
        }
        return file_get_contents(rtrim(site_path, '/') . '/conf.php');
    }

    public function setSiteConfiguration($content){

        if(!ACL::hasPermission('access_admin_site_configuration')){
            return [
                'success' => false,
                'error' => 'Unauthorized'
            ];
        }

        $content = ltrim($content);

        $validation = $this->phpValidator($content);

        // Parse error: syntax error, unexpected identifier "update_issuer", expecting "]" in Standard input code on line 354
        //Errors parsing Standard input code
        if(preg_match('/^Parse error/', $validation)){
            return [
                'success' => false,
                'error' => $validation
            ];
        }

        try {

            eval(preg_replace('/^<\?php/','', $content));

            $written = file_put_contents(rtrim(site_path, '/') . '/conf.php', $content);

            if($written === false){
                return [
                    'success' => false,
                    'error' => 'Unable to write config file'
                ];
            }

        }catch (Exception $e){
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return [
            'success' => true
        ];
    }

    function phpValidator($str) {
        return trim(shell_exec("echo " . escapeshellarg($str) . " | php -l"));
    }

}
