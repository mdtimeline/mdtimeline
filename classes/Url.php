<?php


class Url
{

    public static function shorten($url){

        $curl = curl_init('https://mdtl.io');
//        $curl = curl_init('https://local.tranextgen.com/mdtl/'); // test URL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(['url'=> $url]));
        $link = curl_exec($curl);
        return preg_replace('/<.*?>/', '', $link);
    }

}
