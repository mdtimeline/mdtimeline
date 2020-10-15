<?php
include_once(ROOT . '/vendor/autoload.php');

use Ahc\Jwt\JWT;
use Ahc\Jwt\JWTException;

//use GuzzleHttp\Client;

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
class Client
{

    private $jwt_key = 'Secure#mdtl';
    private $jwt_path = site_temp_path . '/jwtToken.txt';

    private function validateJwtToken($token)
    {
        include_once(ROOT . '/vendor/autoload.php');
        $jwt = new JWT($this->jwt_key, 'HS256');
        try {
            return $jwt->decode($token);
        } catch (JWTException $e) {
            error_log($e);
            return false;
        }
    }

    private function saveToken($token)
    {
        return file_put_contents($this->jwt_path, $token);
    }

    private function getSavedToken()
    {
        return file_get_contents($this->jwt_path);
    }

    private function getAccess($app_key, $domain)
    {
        try {
            $http = new GuzzleHttp\Client();

            $response = $http->post('http://localhost/mdtimeline-auth-server/api/validate', [
                'form_params' => [
                    'appKey' => $app_key,
                    'domain' => $domain,
                ]
            ]);

            if ($response->getStatusCode() != 200) {
                return (object)[
                    'success' => false,
                    'errorMsg' => 'Status not 200. Status: ' . $response->getStatusCode(),
                    'token' => ''
                ];
            }

            $body = $response->getBody();

            return json_decode($body);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            error_log($e->getMessage());

            return (object)[
                'success' => false,
                'errorMsg' => $e->getMessage(),
                'token' => ''
            ];
        }

    }

    public function hasAccess()
    {
        $app_key = defined('app_key') ? app_key : '';
        $domain = defined('HOST') ? HOST : '';

        $response = $this->getAccess($app_key, $domain);

        if (!$response->success) {
            $token = $this->getSavedToken();

            if ($token === false || empty($token)) {
                return [
                    'success' => false,
                    'errorMsg' => 'Token not found',
                ];
            }

            $response->token = $token;
        }

        if ($response->success) $this->saveToken($response->token);

        $payload = $this->validateJwtToken($response->token);

        if ($payload === false) {
            return [
                'success' => false,
                'errorMsg' => 'Invalid Token!',
            ];
        }

        $is_app_active = $payload['active'];
        $start_timestamp = $payload['start_date'];
        $end_timestamp = $payload['end_date'];
        $timer = $payload['timer'];
        $now = Time();
        $license_expire = $now < $start_timestamp || $now > $end_timestamp;

        if (!$is_app_active) {
            return [
                'success' => false,
                'errorMsg' => 'App is suspended.',
            ];
        }

//        if ($license_expire) {
//            $buffer_time = '+15 days';
//            $end_timestamp_plus_buffer_time = strtotime($buffer_time, $end_timestamp);
//
//            if ($now > $end_timestamp && $now <= $end_timestamp_plus_buffer_time) {
//                //TODO show message saying that you should refresh token
//
//                return [
//                    'success' => true,
//                    'errorMsg' => '',
//                ];
//            }
//
//            return [
//                'success' => false,
//                'errorMsg' => 'License Expire',
//            ];
//        }

        if($license_expire){
            sleep($timer);
        }

        return [
            'success' => true,
            'errorMsg' => '',
        ];
    }


}