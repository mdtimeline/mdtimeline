<?php

class Gitter
{

    private $git_username;
    private $git_password;
    private $git_path = '/usr/bin/git';
    private $github_url = '';
    private $bitbucket_url = 'https://[USER]:[PASS]@bitbucket.org/tradev/[REPOSITORY].git';

    function __construct(){
        $this->git_username = $_ENV['git_username'];
        $this->git_password = $_ENV['git_password'];
    }

    public function doLog($repository, $repository_directory = null){
        return $this->gitLog($repository, $repository_directory);
    }

    public function doBranch($repository, $repository_directory = null){
        return $this->gitBranch($repository, $repository_directory);
    }

    public function doBranches($repository, $repository_directory = null){
        return $this->gitBranches($repository, $repository_directory);
    }

    public function doBranchCheckout($branch_name, $repository, $repository_directory = null){
        $branch_name = str_replace("*", "", $branch_name);
        return $this->gitBranchCheckout($branch_name, $repository, $repository_directory);
    }

    public function doDiff($repository, $repository_directory = null){
        return $this->gitDiff($repository, $repository_directory);
    }

    public function doPull($repository, $repository_directory = null){
        return $this->gitPull($repository, $repository_directory);
    }

    public function doReset($repository, $repository_directory = null){
        return $this->gitReset($repository, $repository_directory);
    }

    public function doUpgrade($repository, $repository_directory = null){
        return $this->gitPull($repository, $repository_directory);
    }

    public function doInstall($repository, $repository_directory = null){
        return $this->gitClone($repository, $repository_directory);
    }

    public function doStatus($repository, $repository_directory = null){
        return $this->gitStatus($repository, $repository_directory);
    }

    public function doTags($repository, $repository_directory = null){
        return $this->gitTags($repository, $repository_directory);
    }

    private function gitClone($repository, $repository_directory = null){
        return $this->gitExect('clone', $repository, $repository_directory);
    }

    private function gitPull($repository, $repository_directory = null){
        return $this->gitExect('pull', $repository, $repository_directory);
    }

    private function gitLog($repository, $repository_directory = null){
        return $this->gitExect('log -n 2', $repository, $repository_directory);
    }

    private function gitBranch($repository, $repository_directory = null){
        return $this->gitExect('branch --show-current', $repository, $repository_directory);
    }

    private function gitBranches($repository, $repository_directory = null){
        return $this->gitExect('branch', $repository, $repository_directory);
    }

    private function gitBranchCheckout($branch_name, $repository, $repository_directory = null){
        return $this->gitExect('checkout' . $branch_name, $repository, $repository_directory);
    }

    private function gitDiff($repository, $repository_directory = null){
        return $this->gitExect('diff', $repository, $repository_directory);
    }

    private function gitReset($repository, $repository_directory = null){
        return $this->gitExect('reset --hard', $repository, $repository_directory);
    }

    private function gitStatus($repository, $repository_directory = null){
        return $this->gitExect('status', $repository, $repository_directory);
    }

    private function gitTags($repository, $repository_directory = null){
        return $this->gitExect('tag', $repository, $repository_directory);
    }

    private function gitExect($git_cmd, $repository, $repository_directory = null){
        $output = null;
        $result_code = null;
        $this->changeRepositoryDir($repository, $repository_directory);

        $cmd = $this->getCmd($git_cmd, $repository);

        exec($cmd, $output, $result_code);
        return [
            'output' => $output,
            'result_code' => $result_code
        ];
    }

    private function getCmd($git_cmd, $repository){
        $cmd = "{$this->git_path} {$git_cmd} ";

        if($git_cmd === 'pull' || $git_cmd === 'clone'){
            $cmd .= ($repository === '' || $repository === 'core' ? $this->github_url : $this->bitbucket_url);

            if($git_cmd === 'clone'){
                $cmd .= ' .';
            }

        }

        return str_replace(
            ['[USER]', '[PASS]', '[REPOSITORY]'],
            [$this->git_username, $this->git_password, $repository],
            "$cmd"
        );
    }

    private function changeRepositoryDir($module, $repository_directory = null){

        if(isset($repository_directory) && file_exists($repository_directory)){
            chdir($repository_directory);
        }else{
            $directory = $module === '' ? ROOT : (ROOT . "/modules/{$module}");
            if(!file_exists($directory)){
                mkdir($directory, 0755);
                chmod($directory,0755);
            }
            chdir($directory);
        }
    }

}