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

class ContentManagement {

    private $t;

    function __construct() {
        $this->t = MatchaModel::setSenchaModel('App.model.administration.ContentManagement');
    }

    public function getContentManagements($params){
        $record = $this->t->load($params)->all();
        return $record;
    }
    public function getContentManagement($params){
        return $this->t->load($params)->one();
    }
    public function addContentManagement($params){
        return $this->t->save($params);
    }
    public function updateContentManagement($params){
        return $this->t->save($params);
    }
    public function destroyContentManagement($params){
        return $this->t->save($params);
    }

    public function generateContentManagement($template_type, $language, $placeholders, $values, $pid =  null){

        $template = $this->getContentManagement(['content_type' => $template_type,'content_lang' => $language]);

        if(isset($pid)){
            /*
             *
            '[PATIENT_NAME]',
            '[PATIENT_ID]',
            '[PATIENT_RECORD_NUMBER]',
            '[PATIENT_FULL_NAME]',
            '[PATIENT_LAST_NAME]',
            '[PATIENT_SEX]',
            '[PATIENT_BIRTHDATE]',
            '[PATIENT_MARITAL_STATUS]',
            '[PATIENT_SOCIAL_SECURITY]',
            '[PATIENT_EXTERNAL_ID]',
            '[PATIENT_DRIVERS_LICENSE]',
            '[PATIENT_POSTAL_ADDRESS_LINE_ONE]',
            '[PATIENT_POSTAL_ADDRESS_LINE_TWO]',
            '[PATIENT_POSTAL_CITY]',
            '[PATIENT_POSTAL_STATE]',
            '[PATIENT_POSTAL_ZIP]',
            '[PATIENT_POSTAL_COUNTRY]',
            '[PATIENT_PHYSICAL_ADDRESS_LINE_ONE]',
            '[PATIENT_PHYSICAL_ADDRESS_LINE_TWO]',
            '[PATIENT_PHYSICAL_CITY]',
            '[PATIENT_PHYSICAL_STATE]',
            '[PATIENT_PHYSICAL_ZIP]',
            '[PATIENT_PHYSICAL_COUNTRY]',
            '[PATIENT_HOME_PHONE]',
            '[PATIENT_MOBILE_PHONE]',
            '[PATIENT_WORK_PHONE]',
            '[PATIENT_EMAIL]',
            '[PATIENT_MOTHERS_NAME]',
            '[PATIENT_GUARDIANS_NAME]',
            '[PATIENT_EMERGENCY_CONTACT]',
            '[PATIENT_EMERGENCY_PHONE]',
            '[PATIENT_PROVIDER]',
            '[PATIENT_PHARMACY]',
            '[PATIENT_AGE]',
            '[PATIENT_OCCUPATION]',
            '[PATIENT_EMPLOYEER]',
            '[PATIENT_RACE]',
            '[PATIENT_ETHNICITY]',
            '[PATIENT_LENGUAGE]',
            '[PATIENT_PICTURE]',
            '[PATIENT_QRCODE]',
             */




        }


        if($template !== false){
            $text_template = str_replace($placeholders, $values, $template['content_body']);
            return $text_template;
        }

        return false;
    }


}