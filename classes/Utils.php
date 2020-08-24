<?php


class Utils
{

    public static function dateTimeToString($dateTime, $language, $format_es = 'j \d\e F \d\e\l Y', $format_en = 'F j, Y')
    {
        if ($language == 'en') {

            if (is_string($dateTime)) $dateTime = date($format_en, strtotime($dateTime));
            if ($dateTime instanceof DateTime) $dateTime = date($format_en, $dateTime);

        } else {

            if (is_string($dateTime)) $dateTime = date($format_es, strtotime($dateTime));
            if ($dateTime instanceof DateTime) $dateTime = date($format_es, $dateTime);

            $ts = array(
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
            );

            $tr = array(
                'domingo',
                'lunes',
                'martes',
                'mi&eacute;rcoles',
                'jueves',
                'viernes',
                's&aacute;bado',
                'enero',
                'febrero',
                'marzo',
                'abril',
                'mayo',
                'junio',
                'julio',
                'agosto',
                'septiembre',
                'octubre',
                'noviembre',
                'diciembre',
            );
            $dateTime = str_replace($ts, $tr, $dateTime);
        }
        return $dateTime;
    }

    public static function getOS()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        if(strpos($user_agent, "Win") !== FALSE)
            return "WIN";
        elseif(strpos($user_agent, "Mac") !== FALSE)
            return "MAC";
        else
            return "LINUX";
    }

    public static function isBinary($document)
    {
        if (function_exists('is_binary')) {
            return is_binary($document);
        }
        return preg_match('~[^\x20-\x7E\t\r\n]~', $document) > 0;
    }

    public static function base64ToBinary($document)
    {
        // handle binary documents
        if (self::isBinary($document)) {
            return $document;
        } else {
            return base64_decode($document);
        }
    }
}