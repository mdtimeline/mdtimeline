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

include_once(ROOT . '/lib/tcpdf/tcpdf.php');
include_once(ROOT . '/lib/FPDI/fpdi.php');

class DocumentFPDI extends FPDI  {

	protected $_tplIdx;
	/**
	 * @var array
	 */
	protected $header_data = [];
	/**
	 * @var array
	 */
	protected $footer_data = [];

	/**
	 * @var bool
	 */
	protected $header_line = true;

	/**
	 * @var bool
	 */
	protected $page_number = true;

	/**
	 * @var int
	 */
	protected $header_y = 0;

	/**
	 * @var int
	 */
	protected $footer_y = 0;

	/**
	 * @var array
	 */
	private $header_cols;

	/**
	 * @var int
	 */
	private $center_point;

	/**
	 * @var string
	 */
	public $water_mark = '';

	/**
	 * @var array
	 */
	public $original_margins;

	/**
	 * @param array $data
	 */
	public function addCustomHeaderData(array $data){
		$this->header_data = array_merge($this->header_data, $data);
	}
	/**
	 * @param array $data
	 */
	public function addCustomFooterData(array $data){
		$this->footer_data = array_merge($this->footer_data, $data);
	}

	/**
	 * @param bool $enable
	 */
	public function setCustomHeaderLine($enable){
		$this->header_line = $enable;
	}

	/**
	 * @param bool $enable
	 */
	public function setCustomPageNumber($enable){
		$this->page_number = $enable;
	}

	/**
	 * @return int
	 */
	public function getHeaderY(){
		return $this->header_y;
	}

	public function getCenter(){
		return $this->center_point;
	}

	/**
	 * @return array
	 */
	private function getHeaderCols(){

		if(!isset($this->original_margins)){
			$this->original_margins = $this->getMargins();
		}

		$pageWidth = $this->getPageWidth();
		$columnsWidth =  (($pageWidth - ($this->original_margins['left'] + $this->original_margins['right'])) / 2);
		$center = $this->original_margins['left'] + $columnsWidth;
		$this->center_point = $center;

		if(!isset($this->header_cols)){
			$this->header_cols = [
				'left' => [
					'x' => $this->original_margins['left'],
					'w' => $columnsWidth
				],
				'right' => [
					'x' => $center,
					'w' => $columnsWidth
				],
				'center' => [
					'x' => $this->original_margins['left'] + ($columnsWidth / 2),
					'w' => $columnsWidth
				]
			];
		}

		return $this->header_cols;
	}

	//Page header
	public function Header() {

		if(!isset($this->original_margins)){
			$this->original_margins = $this->getMargins();
		}
		$pageWidth = $this->getPageWidth();
		$pageHeight = $this->getPageHeight();
		$header_cols = $this->getHeaderCols();

		if($this->water_mark != ''){
			$this->addWaterMark();
		}

		$this->header_y = 0;

		if (is_null($this->_tplIdx)) {
			$this->_tplIdx = $this->importPage(1);
		}
		$this->useTemplate($this->_tplIdx, 0, 0, $pageWidth, $pageHeight);

		if(!empty($this->header_data)){

			foreach($this->header_data as $line){

				$line['y'] = $line['y'] + $this->original_margins['top'];

				if(isset($line['col']) && isset($header_cols[$line['col']])){
					$line['x'] = $header_cols[$line['col']]['x'];
					$line['w'] = $header_cols[$line['col']]['w'];
				}

				$this->SetFont($line['font'], '', $line['font_size']);
				$this->SetY($line['y']);
				$this->SetX($line['x']);

				if(isset($line['font_color'])){
					$rbg = $this->hex2RGB($line['font_color']);
					$this->SetTextColor($rbg['red'],$rbg['green'],$rbg['blue']);
				}

				if(isset($line['text']) && isset($line['w']) && isset($line['h'])){
					$this->Cell($line['w'], $line['h'], $line['text'], $line['border'], 0, $line['text_align'], 0, '', 0, false, 'T', 'M');
				}

				if($this->header_y < $line['y']){
					$this->header_y = $line['y'];
				}
			}

			$this->header_y = $this->header_y + 8;
			if($this->header_line){
				$this->Line($this->original_margins['left'], $this->header_y, $pageWidth - $this->original_margins['right'], $this->header_y);
			}
		}else{
			$this->header_y = $this->original_margins['top'];
		}

		$this->SetMargins($this->original_margins['left'], $this->header_y + 10, $this->original_margins['right'], true);
	}

	private function addWaterMark() {
		//Put the watermark
		$this->SetFont('times', 'B', 50);
		$this->SetTextColor(255, 192, 203);

		$margins = $this->getMargins();
		$pageWidth = $this->getPageWidth();

		$water_x = $this->header_cols['center']['x'] /2;
		$water_y = $this->getPageHeight() / 4;

		$this->writeWaterMark($water_x, $water_y);
		$this->writeWaterMark($water_x, $water_y + $water_y);
		$this->writeWaterMark($water_x, $water_y + $water_y + $water_y);
	}


	private function writeWaterMark($water_x, $water_y) {
		$buff = explode('^', $this->water_mark);
		foreach ($buff as $text){
			$this->Text(
				$water_x,
				$water_y,
				$text,
				false,
				false,
				true,
				0,
				0,
				'C'
			);
			$water_y = $water_y + 15;
		}
	}

	// Page footer
	public function Footer(){

		$margins = $this->getMargins();
		$page_height = $this->getPageHeight();
		$page_width = $this->getPageWidth();
		$footer_margin = $page_height - $this->getFooterMargin();
		$header_cols = $this->getHeaderCols();
		$footer_y = $footer_margin;

		if(!empty($this->footer_data)){

			foreach($this->footer_data as $line){

				$line['y'] = $footer_y + $line['y'];

				if(isset($line['col']) && isset($header_cols[$line['col']])){
					$line['x'] = $header_cols[$line['col']]['x'];
					$line['w'] = $header_cols[$line['col']]['w'];
				}

				$this->SetFont($line['font'], '', $line['font_size']);
				$this->SetY($line['y']);
				$this->SetX($line['x']);

				if(isset($line['font_color'])){
					$rbg = $this->hex2RGB($line['font_color']);
					$this->SetTextColor($rbg['red'],$rbg['green'],$rbg['blue']);
				}

				if(isset($line['text']) && isset($line['w']) && isset($line['h'])){
					$this->Cell($line['w'], $line['h'], $line['text'], $line['border'], 0, $line['text_align'], 0, '', 0, false, 'T', 'M');
				}

				if($this->footer_y < $line['y']){
					$this->footer_y = $line['y'];
				}
			}

			$this->footer_y = $footer_y - 8;

		}

		$footerLineY = $footer_margin - 0.5 * 15 - 2;
		$this->SetLineStyle(array('width' => 0.2, 'color' => array(0,0,0)));
		$this->Line($margins['left'], $footerLineY, $page_width - $margins['right'], $footerLineY);
		$this->SetFont('times', '', 9);
		$this->SetY($footer_margin - 0.5 * 15);
		$this->SetX($margins['left']);
		$this->Cell(100, 0, 'Created by mdTimeline (Electronic Health System) v' . VERSION);
		$this->SetX((($page_width - $margins['right']) - 12));
		if($this->page_number){
			$pageText = trim('Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages());
			$this->Cell(12, 0, $pageText, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
		}
	}

	/**
	 * @return string
	 */
	protected function _getxobjectdict(){
		return parent::_getxobjectdict();
	}

	protected function _encrypt_data($n, $s){
		return parent::_encrypt_data($n, $s);
	}

	function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
	}


}
