<?php

/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, LLC.
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

class Network {

	/**
	 * Whether to use proxy addresses or not.
	 *
	 * As default this setting is disabled - IP address is mostly needed to increase
	 * security. HTTP_* are not reliable since can easily be spoofed. It can be enabled
	 * just for more flexibility, but if user uses proxy to connect to trusted services
	 * it's his/her own risk, only reliable field for IP address is $_SERVER['REMOTE_ADDR'].
	 *
	 * @var bool
	 */
	protected static $useProxy = false;

	/**
	 * List of trusted proxy IP addresses
	 *
	 * @var array
	 */
	protected static $trustedProxies = [];

	/**
	 * HTTP header to introspect for proxies
	 *
	 * @var string
	 */
	protected static $proxyHeader = 'HTTP_X_FORWARDED_FOR';

	/**
	 * Returns client IP address.
	 *
	 * @return string IP address.
	 */
	public static function getIpAddress(){
		$ip = self::getIpAddressFromProxy();
		if($ip){
			return $ip;
		}

		// direct IP address
		if(isset($_SERVER['REMOTE_ADDR'])){
			return $_SERVER['REMOTE_ADDR'];
		}

		return '';
	}

	public static function isLocalAddress($ip = null){

		if(!isset($ip)){
			$ip = self::getIpAddress();
		}

		if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)){
			return true;
		}
		return false;
	}

	/**
	 * Attempt to get the IP address for a proxied client
	 *
	 * @see http://tools.ietf.org/html/draft-ietf-appsawg-http-forwarded-10#section-5.2
	 * @return false|string
	 */
	protected static function getIpAddressFromProxy(){
		if(!self::$useProxy
			|| (isset($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], self::$trustedProxies))
		){
			return false;
		}

		$header = self::$proxyHeader;
		if(!isset($_SERVER[$header]) || empty($_SERVER[$header])){
			return false;
		}

		// Extract IPs
		$ips = explode(',', $_SERVER[$header]);
		// trim, so we can compare against trusted proxies properly
		$ips = array_map('trim', $ips);
		// remove trusted proxy IPs
		$ips = array_diff($ips, self::$trustedProxies);

		// Any left?
		if(empty($ips)){
			return false;
		}

		// Since we've removed any known, trusted proxy servers, the right-most
		// address represents the first IP we do not know about -- i.e., we do
		// not know if it is a proxy server, or a client. As such, we treat it
		// as the originating IP.
		// @see http://en.wikipedia.org/wiki/X-Forwarded-For
		$ip = array_pop($ips);

		return $ip;
	}

}
