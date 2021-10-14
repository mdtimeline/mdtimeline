<?php

class System {


    public function __construct()
    {


    }

    public function report(){

        $report = [];

        foreach ($this->getOperatingSystemInfo() as $item){
            $item['Category'] =  'OS';
            $report[] = $item;
        }
        foreach ($this->getMemory() as $item){
            $item['Category'] =  'Memory';
            $report[] = $item;
        }
        foreach ($this->getCpuLoad() as $item){
            $item['Category'] =  'CPU';
            $report[] = $item;
        }
        foreach ($this->fileSystems() as $item){
            $item['Category'] =  'FileSystem';
            $report[] = $item;
        }
        foreach ($this->getPhpInfo() as $item){
            $item['Category'] =  'PHP';
            $report[] = $item;
        }
        foreach ($this->getApplicationInfo() as $item){
            $item['Category'] =  'Application';
            $report[] = $item;
        }

        return $report;

    }

    /**
     * @return array
     */
    private function getMemory(){

        $data = explode("\n", file_get_contents("/proc/meminfo"));
        $memory_info = [];
        foreach ($data as $line) {
            list($key, $val) = explode(":", $line);
            $val = explode(' ', $val);
            // convert output value KB to GB
            $memory = (intval($val[0]) * 9.5367431640625E-7);
            $memory_info[$key] = $memory . ' GB';
        }

        return $memory_info;
    }

    private function getOperatingSystemInfo(){
        $info = parse_ini_string(shell_exec('cat /etc/lsb-release'));

        return $info;
    }

    /**
     * @return array
     */
    private function getCpuLoad(){

        $load = sys_getloadavg();

        return [
            'ServerLoadOneMinute' => $load[0],
            'ServerLoadFiveMinute' => $load[1],
            'ServerLoadFifteenMinute' => $load[2]
        ];

    }

    /**
     * @return array
     */
    private function fileSystems(){

        $files = [
            'FileSystemSizeRoot' => $this->fileSystemSize(disk_free_space('/'))
        ];

        return $files;

    }

    private function getPhpInfo(){
        $version = phpversion();
        $modules = implode(', ', parse_ini_string(shell_exec('php -m'))['PHP Modules']);

        return [
            [
                'description' => 'PHP Version',
                'value' => $version
            ],
            [
                'description' => 'PHP Modules',
                'value' => $modules
            ]
        ];
    }

    private function getApplicationInfo(){

        $info = [];

        $requirements = [
            'curl',
            'gd',
            'mbstring',
            'xml',
            'soap',
            'soap',
            'zip',
            'mysql',
            'opcache'
        ];
        $modules = shell_exec('php -m');

        foreach ($requirements as $requirement){
            $info['description'] =  "Module {$requirement}";

            if (preg_match("/{$requirement}/", $modules)){
                $info['value'] =  'Installed';
            }else{
                $info['value'] = 'Missing';
            }
        }

        return $info;
    }

    function fileSystemSize($size, array $options=null) {

        $o = [
            'binary' => false,
            'decimalPlaces' => 2,
            'decimalSeparator' => '.',
            'thausandsSeparator' => '',
            'maxThreshold' => false, // or thresholds key
            'sufix' => [
                'thresholds' => ['', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
                'decimal' => ' {threshold}B',
                'binary' => ' {threshold}iB'
            ]
        ];

        if ($options !== null)
            $o = array_replace_recursive($o, $options);

        $count = count($o['sufix']['thresholds']);
        $pow = $o['binary'] ? 1024 : 1000;

        for ($i = 0; $i < $count; $i++)

            if (($size < pow($pow, $i + 1)) ||
                ($i === $o['maxThreshold']) ||
                ($i === ($count - 1))
            )
                return

                    number_format(
                        $size / pow($pow, $i),
                        $o['decimalPlaces'],
                        $o['decimalSeparator'],
                        $o['thausandsSeparator']
                    ) .

                    str_replace(
                        '{threshold}',
                        $o['sufix']['thresholds'][$i],
                        $o['sufix'][$o['binary'] ? 'binary' : 'decimal']
                    );
    }


}