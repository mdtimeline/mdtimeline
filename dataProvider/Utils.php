<?php


class Utils
{

    public static function dateTimeToStringSpanish($dateTime, $format = 'j \d\e F \d\e\l Y'){
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

        return str_replace($ts, $tr, $dateTime);
    }

}