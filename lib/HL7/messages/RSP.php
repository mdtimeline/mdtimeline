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
include_once(dirname(__FILE__) . '/Message.php');

class RSP extends Message {

	function __construct($hl7) {
		parent::__construct($hl7);
	}

	function __destruct() {
		parent::__destruct();
	}

	protected function Events($event) {
		$events = array(
			'K11' => array(
				'MSH' => array('required' => true),
				'SFT' => array('repeatable' => true),
				'MSA' => array('repeatable' => true),
				'ERR' => array(),
				'QAK' => array('repeatable' => true),
				'QPD' => array('repeatable' => true),
				'ROW_DEFINITION' => array(
					'required' => true,
					'items' => array(
						'PATIENT' => array(
							'items' => array(
								'PID' => array('required' => true),
								'PD1' => array('repeatable' => true),
								'NTE' => array('repeatable' => true),
								'NK1' => array('repeatable' => true),
								'VISIT' => array(
									'items' => array(
										'PV1' => array('required' => true),
										'PV2' => array(),
									)
								)
							),
						),
						'ORDER_OBSERVATION' => array(
							'required' => true,
							'repeatable' => true,
							'items' => array(
								'ORC' => array('required' => true),
								'OBR' => array(),
								'NTE' => array('repeatable' => true),
								'TIMING_QTY' => array(
									'repeatable' => true,
									'items' => array(
										'TQ1' => array('required' => true),
										'TQ2' => array('repeatable' => true)
									)
								),
								'RXA' => array('required' => true),
								'RXR' => array(),
								'OBSERVATION' => array(
									'repeatable' => true,
									'items' => array(
										'OBX' => array('required' => true),
										'NTE' => array('repeatable' => true)
									)
								)
							)

						)
					)
				)
			)
		);

		return $events[$event];
	}
}