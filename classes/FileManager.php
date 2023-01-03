<?php
/**
GaiaEHR (Electronic Health Records)
Copyright (C) 2013 Certun, LLC.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once ('../session.php');

class FileManager
{
    public $workingDir;
    public $workingDirName;
    public $tempDir;
    public $fileName;
    public $fileExtension;
    public $error = '';
    public $src;

    /**
     * FileManager constructor.
     * At start please check the temporary directory located in "sites", check if a temp
     * directory exists if not, created it, and also check the permission if the
     * directory is not writable, go ahead and do it.
     */
    function __construct()
    {
        try
        {
            $this->tempDir = site_temp_path . '/';
            $oldmask = umask(0);
            if(!file_exists($this->tempDir)) mkdir($this->tempDir, 0774, true);
            if(!is_writable($this->tempDir)) chmod($this->tempDir, 0774);
            umask($oldmask);
        }
        catch(Exception $Error)
        {
            return $Error;
        }
    }

    public function cleanUp()
    {
//        if (is_dir($this->workingDir)) {
//            $this->deleteWorkingDir();
//        }
    }

    public function moveUploadedFileToTempDir($file)
    {
        $this->setFileExtensionFromFile($file['filePath']['name']);
        $this->setSrc();
        if (move_uploaded_file($file['filePath']['tmp_name'], $this->src)) {
            return true;
        } else {
            $this->error = 'Unable to move uploaded file to /temp directory';
            return false;
        }
    }

    public function moveUploadedFileToDir($file, $dir)
    {
        if (move_uploaded_file($file['filePath']['tmp_name'], $dir . $this->setFileExtensionFromFile($file['filePath']['name']))) {
            return true;
        } else {
            return false;
        }
    }

    public function extractUploadedFileToTempDir($file)
    {
        $this->setSrc();
        if ($this->extractFileToTempDir($file['filePath']['tmp_name'])) {
            return true;
        } else {
            $this->error = 'Unable to extract zipped file to /temp directory';
            return false;
        }
    }

    public function extractFileToTempDir($file, $deleteSrcFile = false)
    {
        if ($this->setWorkingDir()) {
            return $this->extractFileToDir($file, $this->workingDir, $deleteSrcFile);
        } else {
            $this->error = 'Unable to create working directory';
            return false;
        }
    }

    public function extractFileToDir($file, $toDir, $deleteSrcFile = false)
    {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($file) === true) {
                $toDir = $toDir . '/';
                $zip->extractTo($toDir);
                $zip->close();
                if ($deleteSrcFile) {
                    $this->deleteFileBySrc($file);
                }
                return $this->workingDir;
            } else {
                $this->error = 'Unable to open zipped file ' . $file;
                return false;
            }
        } else {
            $this->error = 'Php class ZipArchive required';
            return false;
        }
    }

    public function setWorkingDir()
    {
        $workingDir = $this->tempDir . $this->getTempDirAvailableName();
        if (!is_dir($workingDir)) {
            if (mkdir($workingDir, 0774, true)) {
                chmod($workingDir, 0774);
                $this->workingDir = $workingDir;
                return true;
            } else {
                $this->error = 'Unable to write on /temp directory';
                return false;
            }
        } else {
            $this->error = $workingDir . ' exist';
            return false;
        }
    }

    public function chmodReclusive($dir, $mode)
    {
        if (!is_dir($dir)) {
            return chmod($dir, $mode);
        }
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != '.' && $file != '..') {
                $fullPath = $dir . '/' . $file;
                if (!is_dir($fullPath)) {
                    if (!chmod($fullPath, $mode)) {
                        return true;
                    }
                } else {
                    if (!$this->chmodReclusive($fullPath, $mode)) {
                        return false;
                    }
                }
            }
        }
        closedir($dh);
        if (chmod($dir, $mode)) {
            return true;
        } else {
            return false;
        }
    }

    public function getSiteTempDir()
    {
        return $this->tempDir;
    }

    public function getTempDirAvailableName()
    {
        $name = time();
        while (file_exists($this->tempDir . $name)) {
            $name = time();
        }
        $this->workingDirName = $name;
        return $this->workingDirName;
    }

    public function getWorkingDirName()
    {
        return $this->workingDirName;
    }

    private function setFileExtensionFromFile($fileName)
    {
        $foo = explode('.', $fileName);
        return $this->fileExtension = '.' . end($foo);
    }

    private function setSrc()
    {
        $this->src = $this->tempDir . $this->fileName . $this->fileExtension;
        return;
    }

    private function deleteWorkingDir()
    {
        return $this->rmdir_recursive($this->workingDir);
    }

    public function deleteFileBySrc($src)
    {
        return $this->rmdir_recursive($src);
    }

    public static function scanDir($dir, $readmeFiles = false)
    {
        $files = scandir($dir);
        array_shift($files);
        // get rid of '.'
        array_shift($files);
        // get rid of '..'
        if (!$readmeFiles) {
            $count = 0;
            foreach ($files as $file) {
                if (strtolower($file) == 'readme.md' || strtolower($file) == 'readme' || strrpos($file, '.') === 0) {
                    unset($files[$count]);
                }
                $count++;
            }
        }
        return $files;
    }

	public static function zip_dir($dir, $zip_file, $unlink_original){

		// Initialize archive object
		$zip = new ZipArchive();
		$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file)
		{
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($dir) + 1);

				// Add current file to archive
				$zip->addFile($filePath, $relativePath);

				$zip->setExternalAttributesName($relativePath, ZipArchive::OPSYS_UNIX, fileperms($filePath) << 16);

			}
		}

		// Zip archive will be created only after closing object
		$zip->close();

		if($unlink_original){
			self::rmdir_recursive($dir);
		}
	}

    public static function rmdir_recursive($dir)
    {
        $files = scandir($dir);
        array_shift($files);
        // remove '.' from array
        array_shift($files);
        // remove '..' from array
        foreach ($files as $file) {
            $file = $dir . '/' . $file;
            if (is_dir($file)) {
                self::rmdir_recursive($file);
                continue;
            }
            unlink($file);
        }
        rmdir($dir);
        return true;
    }

}
