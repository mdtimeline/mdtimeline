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

class AWS {


	/**
	 * @var Aws\Sdk
	 */
	private $Sdk;

	/**
	 * @var \Aws\S3\S3Client
	 */
	private $S3;

	/**
	 * @var \Aws\Glacier\GlacierClient
	 */
	private $Glacier;

	private $key;
	private $secret;
	private $region;
	private $bucket;
	private $storage_class;

	function __construct(){

		include (ROOT . '/lib/Aws/aws-autoloader.php');

		$this->key = Globals::getGlobal('aws_access_key');
		$this->secret = Globals::getGlobal('aws_private_key');
		$this->region = Globals::getGlobal('aws_region');
		$this->bucket = Globals::getGlobal('aws_bucket');
		$this->storage_class = 'STANDARD_IA';

		if($this->key == '' || $this->secret == '' || $this->region == ''){
			return;
		}

		$this->Sdk = new Aws\Sdk([
			'version' => 'latest',
			'region'  => $this->region
		]);

	}

	private function createS3(){
		if(isset($this->S3)) return;
		$this->S3 = $this->Sdk->createS3([
			'ACL' => 'private',
			'credentials' => [
				'key'    => $this->key,
				'secret' => $this->secret
			]
		]);
	}

	private function createGlacier(){
		if(isset($this->S3)) return;
		$this->S3 = $this->Sdk->createGlacier([
			'ACL' => 'private',
			'credentials' => [
				'key'    => $this->key,
				'secret' => $this->secret
			]
		]);
	}

	public function createBucket($bucket){
		$this->S3->createBucket(['Bucket' => $bucket]);
	}

	public function putObject($filename, $file, $prefix = ''){
		$this->createS3();
		$this->createBucket($this->bucket);
		return $this->S3->putObject([
			'Bucket' => $this->bucket,
			'Key'    => ($prefix . $filename),
			'SourceFile'   => $file,
			'ACL' => 'private',
			'StorageClass' => $this->storage_class
		]);
	}

	/**
	 * @param      $directory
	 * @param null $keyPrefix
	 */
	public function uploadDirectory($directory, $keyPrefix = null){
		$this->createS3();
		$this->createBucket($this->bucket);
		$this->S3->uploadDirectory($directory, $this->bucket, $keyPrefix, [
			'params' => [
				'ACL' => 'private'
			],
			'before' => function(\Aws\CommandInterface $command) {
				$command['StorageClass'] = $this->storage_class;
			}
		]);

	}

	/**
	 * @param      $directory
	 * @param null $keyPrefix
	 *
	 * @return \GuzzleHttp\Promise\PromiseInterface
	 */
	public function uploadDirectoryAsync($directory, $keyPrefix = null){
		$this->createS3();
		$this->createBucket($this->bucket);
		return $this->S3->uploadDirectoryAsync($directory, $this->bucket, $keyPrefix, [
			'params' => [
				'ACL' => 'private'
			],
			'before' => function(\Aws\CommandInterface $command) {
				$command['StorageClass'] = $this->storage_class;
			}
		]);

	}




} 