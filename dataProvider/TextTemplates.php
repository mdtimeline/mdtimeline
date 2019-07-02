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

class TextTemplates {

    private $t;

    function __construct() {
        $this->t = MatchaModel::setSenchaModel('App.model.administration.TextTemplate');
    }

    public function getTextTemplates($params){
        return $this->t->load($params)->all();
    }
    public function getTextTemplate($params){
        return $this->t->load($params)->one();
    }
    public function addTextTemplate($params){
        return $this->t->save($params);
    }
    public function updateTextTemplate($params){
        return $this->t->save($params);
    }
    public function destroyTextTemplate($params){
        return $this->t->save($params);
    }

    public function generateTextTemplate($template_type, $placeholders, $values){

        $template = $this->getTextTemplate(['content_type' => $template_type]);

        if($template !== false){
            $text_template = str_replace($placeholders, $values, $template['content_body']);
            return $text_template;
        }

        return false;
    }


}