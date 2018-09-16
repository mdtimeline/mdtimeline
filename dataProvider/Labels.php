<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 9/8/18
 * Time: 8:29 PM
 */

class Labels
{

	private $font_file = '../resources/fonts/Arial.ttf';

	function CreateLabels($data, $height, $width, $dpi = 300){

		$results = [];

		foreach ($data as $label_data){
			$results[] = $this->CreateLabel($label_data, $height, $width, $dpi);
		}

		return $results;

	}


	function CreateLabel($data, $height, $width, $dpi = 300){

		include_once ('../lib/Barcode/Barcode.php');
		include_once ('../lib/phpqrcode/qrlib.php');

		$site = 'default';
		$bg_img = "../sites/{$site}/label-{$height}x{$width}-bg.jpg";
		$label_structure = $this->getStructure($height . '-' . $width);

		$width = $width * $dpi;
		$height = $height * $dpi;

		$has_bg = file_exists($bg_img);

		if($has_bg){
			$im = imagecreatefromjpeg($bg_img);
		}else{
			$im = @imagecreate($width, $height);
		}

		$i_width = imagesx($im);
		$i_height = imagesy($im);

		$black = ImageColorAllocate($im, 0, 0, 0);

		if(!$has_bg){
			$white = imagecolorallocate($im, 255, 255, 255);
			imagefilledrectangle($im,0,0, $i_width, $i_height, $white);
		}



		$data = (array) $data;
		$keys = array_keys($data);
		$values = array_values($data);

		foreach($label_structure as &$structure){
			$structure['text'] = str_replace($keys, $values, $structure['text']);
		}
		unset($structure);

		foreach ($label_structure as $line){

			if($line['type'] === 'barcode'){
				$this->addBarCode($im, $line);
			}elseif($line['type'] === 'qrcode'){
				$this->addQrCode($im, $line);
			}elseif($line['type'] === 'image'){
				$this->addImage($im, $line);
			}else{
				$this->addText($im, $line, $black);
			}
		}

		ob_start();
		$fullpath = null;
		imagejpeg($im, null, 100);
		$label = ob_get_clean();


		return [
			'success' => true,
			'base64data' => base64_encode($label),
			'width' => $i_width,
			'height' => $i_height
		];

	}

	private function addImage($im, $line){

		if(!isset($line['text']) || $line['text'] == '') return;

		$pic_im = imagecreatefromstring(base64_decode($line['data']));
		$pic_width = imagesx($pic_im);
		$pic_height = imagesy($pic_im);
		imagecopymerge($im, $pic_im, $line['x'], $line['y'], 0, 0, $pic_width, $pic_height, 100); //h

	}

	private function addText($im, $line, $black){

		imagettftext(
			$im,
			$line['font_size'],
			$line['angle'],
			$line['x'],
			$line['y'],
			$black,
			$this->font_file,
			$line['text']
		);

	}

	private function addQrCode($im, $code){

		if(!isset($code['text']) || $code['text'] == '') return;

		ob_start();
		QRCode::png($code['text'], false, 'Q', $code['height'], $code['width']);
		$qrcode_ob = ob_get_clean();
		$qrcode_im = imagecreatefromstring ($qrcode_ob);
		$qrcode_width = imagesx($qrcode_im);
		$qrcode_height = imagesy($qrcode_im);

		imagecopymerge($im, $qrcode_im, $code['x'], $code['y'], 0, 0, $qrcode_width, $qrcode_height, 100); //have to play with these numbers for it to work for you, etc.

	}

	private function addBarCode($im, $code){

		if(!isset($code['text']) || $code['text'] == '') return;

		$brcode = new barCode('jpeg', $code['height'],3, 3, 6);
		$i_width = imagesx($im);
		$i_height = imagesy($im);

		ob_start();
		$brcode->build($code['text'], $code['show_text']);
		$brcode_ob = ob_get_clean();
		$brcode_im = imagecreatefromstring ($brcode_ob);
		$brcode_width = imagesx($brcode_im);
		$brcode_height = imagesy($brcode_im);

		if($code['angle'] !== 0){
			$brcode_im = imagerotate($brcode_im, $code['angle'], 0);
			$brcode_width = imagesx($brcode_im);
			$brcode_height = imagesy($brcode_im);
		}

		if($code['x'] === 'center'){
			$code['x'] = ($i_width / 2) - ($brcode_width / 2);
		}
		if($code['y'] === 'center'){
			$code['y'] = ($i_height / 2) - ($brcode_height / 2);
		}

		imagecopymerge($im, $brcode_im, $code['x'], $code['y'], 0, 0, $brcode_width, $brcode_height, 100); //have to play with these numbers for it to work for you, etc.

	}

	private function getStructure($size){

		$structure = [
			'2.3125-4' => [
				[
					'type' => 'barcode',
					'height' => 150,
					'width' => 200,
					'x' => 1000,
					'y' => 'center',
					'angle' => 90,
					'text' => '[RECORD_NUMBER]',
					'show_text' => true
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 300,
					'text' => '[PATIENT_NAME] ([PATIENT_SEX]) ([PATIENT_AGE])',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 340,
					'text' => '[RECORD_NUMBER] ([PATIENT_INSURANCE])',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 370,
					'text' => '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 400,
					'text' => '[REFERRING_PHYSICIAN]',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 640,
					'y' => 400,
					'text' => '[SERVICE_DATE]',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 450,
					'text' => '[MODALITY_1]  [ACCESSION_NUMBER_1]   [STUDY_DESCRIPTION_1]',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 480,
					'text' => '[MODALITY_2]  [ACCESSION_NUMBER_2]   [STUDY_DESCRIPTION_2]',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 510,
					'text' => '[MODALITY_3]  [ACCESSION_NUMBER_3]   [STUDY_DESCRIPTION_3]',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 540,
					'text' => '[MODALITY_4]  [ACCESSION_NUMBER_4]   [STUDY_DESCRIPTION_4]',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 570,
					'text' => '[MODALITY_5]  [ACCESSION_NUMBER_5]   [STUDY_DESCRIPTION_5]',
				]
			],
			'1.1250-3.5' => [
				[
					'type' => 'barcode',
					'height' => 50,
					'width' => 200,
					'x' => 80,
					'y' => 265,
					'angle' => 0,
					'text' => '[RECORD_NUMBER]',
					'show_text' => false
				],
//				[
//					'type' => 'qrcode',
//					'height' => 6,
//					'width' => 6,
//					'x' => 815,
//					'y' => 155,
//					'angle' => 0,
//					'text' => '[ACCESSION_NUMBER_1]'
//				],
				[
					'type' => 'text',
					'font_size' => 32,
					'angle' => 0,
					'x' => 80,
					'y' => 65,
					'text' => '[FACILITY]',
				],
				[
					'type' => 'text',
					'font_size' => 32,
					'angle' => 0,
					'x' => 750,
					'y' => 65,
					'text' => '[SERVICE_DATE]',
				],
//				[
//					'type' => 'text',
//					'font_size' => 24,
//					'angle' => 0,
//					'x' => 80,
//					'y' => 60,
//					'text' => '___________________________',
//				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 120,
					'text' => '[PATIENT_NAME] ([PATIENT_SEX]) ([PATIENT_AGE])',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 160,
					'text' => '[RECORD_NUMBER] ([PATIENT_INSURANCE])',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 200,
					'text' => 'Ref: [REFERRING_PHYSICIAN]',
				],
				[
					'type' => 'text',
					'font_size' => 24,
					'angle' => 0,
					'x' => 80,
					'y' => 240,
					'text' => '[MODALITY_1] [STUDY_DESCRIPTION_1] [ACCESSION_NUMBER_1]',
				]
			]
		];


		return $structure[$size];
	}

}


