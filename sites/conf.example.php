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

if(!defined('site_db_type')) define('site_db_type', 'mysql');
if(!defined('site_db_host')) define('site_db_host', '#host#');
if(!defined('site_db_port')) define('site_db_port', '#port#');
if(!defined('site_db_username')) define('site_db_username', '#user#');
if(!defined('site_db_password')) define('site_db_password', '#pass#');
if(!defined('site_db_database')) define('site_db_database', '#db#');
/**
 * AES Key
 * 256bit - key
 */
if(!defined('site_aes_key')) define('site_aes_key', '#key#');
/**
 * HL7 server values
 */

if(!defined('site_hl7_ports')) define('site_hl7_ports', '#hl7Port#');
/**
 * Default site language and theme
 * Check if the localization variable already has a value, if not pass the
 * default language.
 */
if(!defined('site_name')) define('site_name', '#sitename#');
if(!defined('site_theme')) define('site_theme', '#theme#');
if(!defined('site_timezone')) define('site_timezone', '#timezone#');
if(!defined('site_default_localization')) define('site_default_localization', '#lang#');

if(!defined('site_id')) define('site_id', basename(dirname(__FILE__)));
if(!defined('site_dir')) define('site_dir', site_id);
if(!defined('site_url')) define('site_url', URL .'/sites/'.site_id);
if(!defined('site_path')) define('site_path', str_replace('\\', '/', dirname(__FILE__)));
if(!defined('site_temp_url')) define('site_temp_url', site_url .'/temp');
if(!defined('site_temp_path')) define('site_temp_path', site_path . '/temp');
if(!defined('site_external_url')) {
    define('site_external_url', 'https://#external_url#/');
}
if(!defined('use_openssl')) define('use_openssl', true);

/**
 * Set the timezone of the site, this will affect the complete application
 */
date_default_timezone_set(site_timezone);
ini_set('date.timezone',site_timezone);

$_ENV['module']['ringcentral'] = [
    'client_id' => 'qzkBBbVARWij7PHwSGL2qw',
    'client_secret' => '',
    'phone_number' => '+17875501173',
    'username' => '+15672984482',
    'extension' => '102',
    'password' => 'J@na0920'
];

$_ENV['module']['twilio'] = [
    'sid' => 'AC2188e1d152fab71ac8c09a03b22ed331',
    'token' => '',
    'twilio_phone_number' => '+17873392515',
    'twilio_phone_number_sid' => 'PN735cf284f5999dcfd19430dea182e383',
    'voice_status_callback' => 'https://#external_url#/modules/twilio/api/voice/events.php?',
    'sms_status_callback' => 'https://#external_url#/modules/twilio/api/sms/events.php?',
    'fax_status_callback' => 'https://#external_url#/modules/twilio/api/fax/events.php?',
    'test_mode' => true,
    'test_phone_number' => '+17875501173'
];

$_ENV['azure_auth'] = [
    'enabled' => false,
    'object_id' => '', // Object ID
    'client_id' => '',
    'tenant_id' => '',
    'client_secret' => '',
    'redirect_uri' => 'https://#external_url#/mdtimeline/modules/azure/auth/Callback',
    'scopes' => [
        'openid',
        'profile',
        'email',
        'offline_access',
        'https://graph.microsoft.com/User.Read'
    ],
];

$GLOBALS['dragon_360'] = [
//    'organization_token' => 'fec16273-a8af-46f8-966c-671b4588477b'  # TRA Demo Lic.
//    'organization_token_exceptions' => [
//        'admin' => 'fec16273-a8af-46f8-966c-671b4588477b'
//    ]
];

$GLOBALS['worklist_dbs'] = [
    'default' => [
        'host' => site_db_host,
        'port' => site_db_port,
        'name' => site_db_database,
        'user' => site_db_username,
        'pass' => site_db_password,
        'app' => ROOT . '/app'
    ]
];

$GLOBALS['worklist_pacs'] = [
    'dcm4chee' => [
        'MDTIMELINE' => [
            'name' => 'MDTIMELINE',
            'type' => 'dcm4chee',
            'ip' => '127.0.0.1',
            'port' => '11112',
            'url' => 'http://127.0.0.1:8080',
            'proxy_url' => 'https://#external_url#/MDTIMELINE/wado/',
//			'local_host' => 'local.tranextgen.com/pacs5/aets',
//			'alt_url' => 'https://local.tranextgen.com/pacs5/aets',
            'watcher' => true,
            'insert_studies' => false,
//            'insert_studies' => [
//                'insert_institution_names' => [
//                    'PROFESSIONAL HOSPITAL GUAYNABO',
//                    'PHG' => [
//                        'defaults' => [
//                            'department_id' => 1
//                        ]
//                    ]
//                ]
//            ],
            'orphaned_studies' => false,
            'hl7_url' => '127.0.0.1',
            'hl7_port' => 2575,
            'hl7_sending_application' => 'MDTIMELINE',
            'hl7_sending_facility' => 'MDTIMELINE',
            'hl7_receiving_application' => 'MDTIMELINE',
            'hl7_receiving_facility' => 'MDTIMELINE',
//            'mwl_aet' => 'MDTIMELINE-MWL',
            'ws_url' => 'http://127.0.0.1/pacs5',
            'ws_username' => 'admin',
            'ws_password' => 'admin',
            'site' => 'default',
            'look_back_days' => 30,
            'db_host' => '',
            'db_port' => '',
            'db_user' => '',
            'db_pass' => '',
            'db_name' => '',
//			'pacs_pid_flag' => 'trimmed_no_slash',
//            'pacs_pid_flag' => [
//                'validate' => '/^.{1}-.{15}-.{2}$/',
//                'regex' => '/(.{1})-.*(.{6})-(.*)/',
//                'replacement' => '$1-$2-$3'
//            ],
            'version' => '5'
        ]
    ]
];