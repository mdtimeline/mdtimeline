<?php

class Gitter
{

    private $git_username;
    private $git_password;
    private $git_path = 'git';
    private $github_url = '';
    private $bitbucket_url = 'https://[USER]:[PASS]@bitbucket.org/tradev/[MODULE].git';

    function __construct(){
        $this->git_username = $_ENV['git_username'];
        $this->git_password = $_ENV['git_password'];
    }

    public function doLog($module){
        return $this->gitLog($module);
    }

    public function doDiff($module){
        return $this->gitDiff($module);
    }

    public function doReset($module){
        return $this->gitReset($module);
    }

    public function doUpgrade($module){
        return $this->gitPull($module);
    }

    public function doInstall($module){
        return $this->gitClone($module);
    }

    private function gitClone($module){
        return $this->gitExect('clone', $module);
    }

    private function gitPull($module){
        return $this->gitExect('pull', $module);
    }

    private function gitLog($module){
        return $this->gitExect('log -n 5', $module);
    }

    private function gitDiff($module){
        return $this->gitExect('diff', $module);
    }

    private function gitReset($module){
        return $this->gitExect('reset --hard', $module);
    }

    private function gitExect($git_cmd, $module){
        $output = null;
        $result_code = null;
        $this->changeModuleDir($module);
        $cmd = $this->getCmd($git_cmd, $module);
        exec($cmd, $output, $result_code);
        return [
            'output' => $output,
            'result_code' => $result_code
        ];
    }

    private function getCmd($git_cmd, $module){
        $cmd = "{$this->git_path} {$git_cmd} ";

        if($git_cmd === 'pull' || $git_cmd === 'clone'){
            $cmd .= ($module === '' ? $this->github_url : $this->bitbucket_url);

            if($git_cmd === 'clone'){
                $cmd .= ' .';
            }

        }

        return str_replace(
            ['[USER]', '[PASS]', '[MODULE]'],
            [$this->git_username, $this->git_password, $module],
            "$cmd"
        );
    }

    private function changeModuleDir($module){

        $directory = $module === '' ? ROOT : (ROOT . "/modules/{$module}");

        if(!file_exists($directory)){
            mkdir($directory, 0755);
            chmod($directory,0755);
        }

        chdir($directory);
    }

}