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

class MDM extends Message {

	function __construct($hl7) {
		parent::__construct($hl7);
	}

	function __destruct() {
		parent::__destruct();
	}

	protected function Events($event) {
		$events = array(
			'T02' => array( // Original Document Notification with Content
				'MSH' => array('required' => true),
				'SFT' => array('repeatable' => true),
                'EVN' => array('required' => true),
                'PID' => array('required' => true),
                'PV1' => array('required' => true),
                'COMMON_ORDER' => array(
                    'required' => true,
                    'repeatable' => true,
                    'items' => array(
                        'ORC' => array(
                            'items' => array(
                                'TIMING' => array(
                                    'repeatable' => true,
                                    'items' => array(
                                        'TQ1' => array('required' => true),
                                        'TQ2' => array('repeatable' => true)
                                    )
                                ),
                            )
                        ),
                        'OBR' => array('required' => true),
                        'NTE' => array('repeatable' => true),
                    )
                ),
                'TXA' => array(),
                'OBX' => array(),
                'NTE' => array('repeatable' => true)
			),
			'T06' => array( // Document Addendum Notification with Content
                'MSH' => array('required' => true),
                'SFT' => array('repeatable' => true),
                'EVN' => array('required' => true),
                'PID' => array('required' => true),
                'PV1' => array('required' => true),
                'COMMON_ORDER' => array(
                    'required' => true,
                    'repeatable' => true,
                    'items' => array(
                        'ORC' => array(
                            'items' => array(
                                'TIMING' => array(
                                    'repeatable' => true,
                                    'items' => array(
                                        'TQ1' => array('required' => true),
                                        'TQ2' => array('repeatable' => true)
                                    )
                                ),
                            )
                        ),
                        'OBR' => array('required' => true),
                        'NTE' => array('repeatable' => true),
                    )
                ),
                'TXA' => array(),
                'OBX' => array(),
                'NTE' => array('repeatable' => true)
			)

		);

		return $events[$event];
	}
}