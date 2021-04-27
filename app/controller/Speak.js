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

Ext.define('App.controller.Speak', {
	extend: 'Ext.app.Controller',

	refs: [

	],

	init: function () {

		var me = this;

		me.voices = [];
        me.voice_enabled = false;
        me.voice_lang = 'en';
        me.voice_gender = 'female';

        if(SpeechSynthesisUtterance){
	        me.voices = window.speechSynthesis.getVoices();
        }

    },

	speak: function(message){
        var me = this, msg, voices;

        if(SpeechSynthesisUtterance){
	        voices = window.speechSynthesis.getVoices();
	        msg = new SpeechSynthesisUtterance();
	        msg.voice = me.voice_gender === 'female' ? voices[32] : voices[0];
	        msg.text = message;
	        window.speechSynthesis.speak(msg);
        }
	}


});