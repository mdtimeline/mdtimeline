<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 9/8/18
 * Time: 8:29 PM
 */

class PictureIdCard
{

	private $masked = true;
	private $font_file = ROOT . '/resources/fonts/Arial.ttf';
	private $printer;
	private $img_width;
	private $img_height;


	function Create($data){

		include_once (ROOT .'/lib/phpqrcode/qrlib.php');

		$bg = ROOT . '/sites/'. site_name .'/idcard-bg.jpg';

		$has_bg = file_exists($bg);

		$printer['dpi'] = 600;
		$printer['margin'] = 1;
		$printer['width'] = 3.375;
		$printer['height'] = 2.125;
		$printer['font'] = 12;

		$this->printer = $printer;


		$margin = $printer['dpi'] * $printer['margin'] / 100;
		$img_width = floor($printer['width'] * $printer['dpi']);
		$img_height = floor($printer['height'] * $printer['dpi']);

		if($has_bg){
			$im = imagecreatefromjpeg($bg);
		}else{
			$im = imagecreatetruecolor($img_width, $img_height);
		}

		$img_width = imagesx($im);
		$img_height = imagesy($im);

		$this->img_width = $img_width;
		$this->img_height = $img_height;


		if(!$has_bg){
			$white = imagecolorallocate($im, 255, 255, 255);
			imagefilledrectangle($im,0,0, $img_width, $img_height, $white);
		}

		//include_once ('Patient.php');

		$fontSize = $printer['dpi'] * $printer['font'] / 100;   // GD1 in px ; GD2 in point
		$marge = 10;   // between barcode and hri in pixel
		$x = $img_width / 2;  // barcode center
		$y = $img_height * .74;  // barcode center
		$height = $img_height * .3;   // barcode height in 1D ; module size in 2D
		$width = $img_width * .007;    // barcode height in 1D ; not use in 2D
		$angle = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation


		$color = [
			'black' => ImageColorAllocate($im, 0x00, 0x00, 0x00),
			'white' => ImageColorAllocate($im, 0xff, 0xff, 0xff),
			'red' => ImageColorAllocate($im, 0xff, 0x00, 0x00),
			'blue' =>  ImageColorAllocate($im, 0x00, 0x00, 0xff),		];


		$card_structure = [
			[
				'type' => 'text',
				'font_size' => 10,
				'angle' => 0,
				'x' => 220,
				'y' => 110,
				'color' => 'black',
				'text' => 'Patient:',
			],
			[
				'type' => 'text',
				'font_size' => 10,
				'angle' => 0,
				'x' => 220,
				'y' => 160,
				'color' => 'black',
				'text' => '[PATIENT_NAME]',
			],
			[
				'type' => 'text',
				'font_size' => 10,
				'angle' => 0,
				'x' => 220,
				'y' => 220,
				'color' => 'black',
				'text' => 'Record Number:',
			],
			[
				'type' => 'text',
				'font_size' => 10,
				'angle' => 0,
				'x' => 220,
				'y' => 260,
				'color' => 'black',
				'text' => '[RECORD_NUMBER]',
			],
			[
				'type' => 'text',
				'font_size' => 10,
				'angle' => 0,
				'x' => 220,
				'y' => 330,
				'color' => 'black',
				'text' => 'Facility:',
			],
			[
				'type' => 'text',
				'font_size' => 10,
				'angle' => 0,
				'x' => 220,
				'y' => 370,
				'color' => 'black',
				'text' => '[FACILITY]',
			],
			[
				'type' => 'qrcode',
				'font_size' => 24,
				'angle' => 0,
				'x' => 450,
				'y' => 360,
				'height' => 10,
				'width' => 10,
				'color' => 'black',
				'text' => '[RECORD_NUMBER]',
			],
			[
				'type' => 'image',
				'font_size' => 10,
				'angle' => 0,
				'x' => 20,
				'y' => 80,
				'height' => 100,
				'width' => 100,
				'color' => 'black',
				'text' => '[IMAGE]',
			]
		];
		
		$data = (array) $data;
		$keys = array_keys($data);
		$values = array_values($data);

		foreach($card_structure as &$structure){
			$structure['text'] = str_replace($keys, $values, $structure['text']);
		}

		unset($structure);

		foreach ($card_structure as $structure){
			if($structure['type'] === 'barcode'){
				$this->addBarCode($im, $structure);
			}elseif($structure['type'] === 'qrcode'){
				$this->addQrCode($im, $structure);
			}elseif($structure['type'] === 'image'){
				$this->addImage($im, $structure);
			}else{
				$this->addText($im, $structure, $color[$structure['color']]);
			}
		}


		ob_start();
		$success = imagejpeg($im, null, 100);
		$id_card = ob_get_clean();

		return [
			'success' => $success,
			'base64data' => base64_encode($id_card),
			'width' => $img_width,
			'height' => $img_height
		];

	}

	private function getSize($size){
		return $this->printer['dpi'] * $size / 100;
	}

	private function getPointX($point){
		return $this->img_width * $point / $this->printer['dpi'];
	}

	private function getPointY($point){
		return $this->img_height * $point / $this->printer['dpi'];
	}

	private function addImage($im, $line){
		if(!isset($line['text']) || $line['text'] == '') return;

		$line['text'] = substr($line['text'], strpos($line['text'], ',') + 1);

		$pic_im = imagecreatefromstring(base64_decode($line['text']));
		$pic_width = imagesx($pic_im);
		$pic_height = imagesy($pic_im);

		$line_width = $this->getSize($line['width']);
		$line_height = $this->getSize($line['height']);
		$line_x = $this->getPointX($line['x']);
		$line_y = $this->getPointY($line['y']);



		$resmple_pic_im = imagecreatetruecolor($line_width, $line_height);

		imagecopyresampled($resmple_pic_im, $pic_im, 0, 0, 0, 0, $line_width, $line_height, $pic_width, $pic_height);

		imagecopymerge($im, $resmple_pic_im, $line_x, $line_y, 0, 0, $line_width, $line_height, 100); //h

	}

	private function addText($im, $line, $black){

		imagettftext(
			$im,
			$this->getSize($line['font_size']),
			$line['angle'],
			$this->getPointX($line['x']),
			$this->getPointY($line['y']),
			$black,
			$this->font_file,
			$line['text']
		);

	}

	private function addQrCode($im, $code){
		ob_start();
		QRCode::png($code['text'], false, 'Q', $code['height'], $code['width']);
		$qrcode_ob = ob_get_clean();
		$qrcode_im = imagecreatefromstring ($qrcode_ob);
		$qrcode_width = imagesx($qrcode_im);
		$qrcode_height = imagesy($qrcode_im);

		$code_width = $this->getSize($code['width']);
		$code_height = $this->getSize($code['height']);
		$code_x = $this->getPointX($code['x']);
		$code_y = $this->getPointY($code['y']);

		imagecopymerge($im, $qrcode_im, $code_x, $code_y, 0, 0, $qrcode_width, $qrcode_height, 100); //have to play with these numbers for it to work for you, etc.


	}

	private function addBarCode($im, $code){
		$brcode = new barCode('jpeg', $code['height'],2, 2, 5);
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
}

//$f00 = new PictureIdCard();
//$f00->Create(2);