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

class FileSystem {
	/**
	 * @var MatchaCUP
	 */
	private $fs;

	/**
	 * @var int min free space in GBs
	 */
	private $min_free_space = 50;

	function __construct() {
		$this->fs = MatchaModel::setSenchaModel('App.model.administration.FileSystem');
	}

	public function getFileSystems($params) {
		return $this->fs->load($params)->all();
	}
	public function getFileSystem($params) {
		return $this->fs->load($params)->one();
	}

	public function addFileSystem($params) {
		return $this->fs->save($params);
	}

	public function updateFileSystem($params) {
		return $this->fs->save($params);
	}

	public function getOnlineFileSystem() {

		if(isset($_SESSION['file_system'])){
			return $_SESSION['file_system'];
		}

		$this->fs->addFilter('status', 'ACTIVE');
		$filesystem = $this->fs->load()->one();
		$this->fs->clearFilters();

		if($filesystem === false) return $filesystem;

		if($this->isFull($filesystem)){

			$this->fs->addFilter('id', $filesystem['next_id']);
			$next_filesystem = $this->fs->load()->one();
			$this->fs->clearFilters();

			if($next_filesystem === false){
				error_log('FILE SYSTEM FULL - '. $next_filesystem['dir_path']);

				$_SESSION['file_system'] = $filesystem;
				return $filesystem;
			}

			$next_filesystem['status'] = 'ACTIVE';
			$this->updateFileSystem((object) $next_filesystem);

			$filesystem['status'] = 'FULL';
			$this->updateFileSystem((object) $filesystem);

			$_SESSION['file_system'] = $next_filesystem;
			return $next_filesystem;
		}

		$_SESSION['file_system'] = $filesystem;
		return $filesystem;
	}

	public function getFileSystemPath($filesystem_id) {
		$this->fs->addFilter('id', $filesystem_id);
		$filesystem = $this->fs->load()->one();
		if($filesystem === false) return '';
		return $filesystem['dir_path'];
	}

	public function getOnlineFileSystemPath() {
		$filesystem = $this->getOnlineFileSystem();
		if($filesystem === false) return '';
		return $filesystem['dir_path'];
	}

	public function fileSystemsSpaceAnalyzer(){
		$filesystems = $this->getFileSystems(null);
		foreach($filesystems as &$filesystem){
			if(!isset($filesystem['dir_path']) || !file_exists($filesystem['dir_path'])) continue;
			$bytes = disk_total_space($filesystem['dir_path']);
			$filesystem['total_space'] = $bytes ? round($bytes/1024/1024/1024, 1) : 0;

			$bytes = disk_free_space($filesystem['dir_path']);
			$filesystem['free_space'] = $bytes ? round($bytes/1024/1024/1024, 1) : 0;

			$this->updateFileSystem((object) $filesystem);
		}
		return $filesystems;
	}

	private function isFull($filesystem){
		return intval($filesystem['free_space']) < $this->min_free_space;
	}
}
