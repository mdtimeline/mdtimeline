<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 9/8/18
 * Time: 8:29 PM
 */

class Labels
{

	/**
	 * @var MatchaCUP
	 */
	private $l;

	private $font_file = '../resources/fonts/Arial.ttf';

	function __construct(){
		$this->l = MatchaModel::setSenchaModel('App.model.administration.Label');
	}

	public function getLabels($params){
		return $this->l->load($params)->all();
	}

	public function getLabel($params){
		return $this->l->load($params)->one();
	}

	public function addLabel($params){
		return $this->l->save($params);
	}

	public function updateLabel($params){
		return $this->l->save($params);
	}

	public function destroyLabel($params){
		return $this->l->destroy($params);
	}

	private function getStructure($label_type, $label_size){
		return $this->getLabels(['label_type' => $label_type, 'label_size' => $label_size]);
	}

	public function CreateLabels($label_type, $data, $height, $width, $dpi = 300){

		$results = [];

		$facility_data = (object)$this->getFacilityTokens();
		foreach ($data as $label_data){
            $label_data = (object) array_merge((array) $label_data, (array) $facility_data);
			$results[] = $this->CreateLabel($label_type, $label_data, $height, $width, $dpi);
		}

		return $results;

	}

	public function CreateLabel($label_type, $data, $height, $width, $dpi = 300){

		include_once ('../lib/phpqrcode/qrlib.php');
        $facility_id = $_SESSION['user']['facility'];

        $site = 'default';
		$label_size = $height . 'x' . $width;
		$bg_img = "../sites/{$site}/label-{$label_size}-bg.jpg";
		$bg_img_facility = "../sites/{$site}/label-{$label_size}-bg-{$facility_id}.jpg";
		$label_structure = $this->getStructure($label_type, $label_size);

		// fix margins
		$width = $width - .3;
		$height = $height - .18;

		$width = $width * $dpi;
		$height = $height * $dpi;

        $has_bg = false;

        if(file_exists($bg_img_facility)){
            $has_bg = true;
            $bg_img = $bg_img_facility;
        }elseif (file_exists($bg_img)){
            $has_bg = true;
        }

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

		include_once ('../lib/Barcode/Barcode2.php');

		// try to shrink the record number
		$text_array = explode('-', $code['text']);
		if(isset($text_array[1])){
			$text_array[1] = ltrim($text_array[1], '0');
			$code['text'] = implode('-', $text_array);
		}

		$generator = new barcode_generator();

		/* Create bitmap image. */
		$brcode_im = $generator->render_image('code-39', $code['text'], [
			'f' => 'jpeg',
			'h' => $code['height'],
			'w' => $code['width'],
			'ts' => $code['font_size'],
			'wn' => 1,
		]);

		$brcode_width = imagesx($brcode_im);
		$brcode_height = imagesy($brcode_im);

		if($code['angle'] !== 0){
			$brcode_im = imagerotate($brcode_im, $code['angle'], 0);
			$brcode_width = imagesx($brcode_im);
			$brcode_height = imagesy($brcode_im);
		}

		$i_width = imagesx($im);
		$i_height = imagesy($im);

		if($code['x'] === 'center'){
			$code['x'] = ($i_width / 2) - ($brcode_width / 2);
		}
		if($code['y'] === 'center'){
			$code['y'] = ($i_height / 2) - ($brcode_height / 2);
		}

		imagecopymerge($im, $brcode_im, $code['x'], $code['y'], 0, 0, $brcode_width, $brcode_height, 100); //have to play with these numbers for it to work for you, etc.

	}

	private function getFacilityTokens($facility_id = null){
        $facility_id = isset($facility_id) ? $facility_id : $_SESSION['user']['facility'];

        include_once (ROOT. '/dataProvider/Facilities.php');
        $Facilities = new Facilities();
        $facility = $Facilities->getFacilityById($facility_id);

        if($facility === false){
            return [];
        }

        return [
            '[FACILITY_ID]' => $facility['id'],
            '[FACILITY_EXTERNAL_ID]' => isset($facility['external_id']) ? $facility['external_id'] : '',
            '[FACILITY_CODE]' => isset($facility['code']) ? $facility['code'] : '',
            '[FACILITY_NAME]' => isset($facility['name']) ? $facility['name'] : '',
            '[FACILITY_NAME_LEGAL]' => isset($facility['legal_name']) ? $facility['legal_name'] : '',
            '[FACILITY_ATT]' => isset($facility['attn']) ? $facility['attn'] : '',
            '[FACILITY_REGION]' => isset($facility['region']) ? $facility['region'] : '',
            '[FACILITY_PHONE]' => isset($facility['phone']) ? $facility['phone'] : '',
            '[FACILITY_FAX]' => isset($facility['fax']) ? $facility['fax'] : '',
            '[FACILITY_EMAIL]' => isset($facility['email']) ? $facility['email'] : '',
            '[FACILITY_ADDRESS_ONE]' => isset($facility['address']) ? $facility['address'] : '',
            '[FACILITY_ADDRESS_TWO]' => isset($facility['address_cont']) ? $facility['address_cont'] : '',
            '[FACILITY_ADDRESS_CITY]' => isset($facility['city']) ? $facility['city'] : '',
            '[FACILITY_ADDRESS_STATE]' => isset($facility['state']) ? $facility['state'] : '',
            '[FACILITY_ADDRESS_ZIPCODE]' => isset($facility['postal_code']) ? $facility['postal_code'] : '',
            '[FACILITY_ADDRESS_NPI]' => isset($facility['npi']) ? $facility['npi'] : ''
        ];


    }
}


