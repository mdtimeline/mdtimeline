/**
 GaiaEHR (Electronic Health Records)
 Copyright (C) 2013 Certun, LLC.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Ext.define('App.view.patient.ProgressNote', {
	extend           : 'Ext.panel.Panel',
    alias            : 'widget.progressnote',
    bodyPadding      : 5,
    autoScroll       : true,
    loadMask         : false,
	initComponent: function() {
		var me = this;

        me.tpl = new Ext.XTemplate(
            '<div class="progressNote" style="margin: 5px">' +
            '   <div class="secession general-data">' +
            '       <div class="title"> ' + _('general') + ' </div>' +
            '       <table width="100%">' +
            '           <tr>' +
            '               <td>' +
            '                   <div class="header row">' + _('name') + ': {patient_name} </div>' +
            '                   <div class="header row">' + _('record') + ': #{pid} </div>' +
            '                   <div class="header row">' + _('provider_date') + ': {open_by} </div>' +
            '                   <div class="header row">' + _('onset_date') + ': {[values.onset_date || "-"]} </div>' +
            '                   <div class="header row">' + _('provider') + ': {[values.signed_by || "-"]} </div>' +
            '               </td>' +
            '               <td>' +
            '                   <div class="header row">' + _('service_date') + ': {service_date} </div>' +
            '                   <div class="header row">' + _('visit_category') + ': {visit_category} </div>' +
            '                   <div class="header row">' + _('facility') + ': {facility} </div>' +
            '                   <div class="header row">' + _('priority') + ': {priority} </div>' +
            '                   <div class="header row">' + _('signed_on') + ': {[values.close_date || "-"]} </div>' +
            '               </td>' +
            '           </tr>' +
            '           <tr>' +
            '               <td colspan="2">' +
            '                   <div class="header row" style="white-space: normal;">' + _('chief_complaint') + ': {brief_description} </div>' +
            '               </td>' +
            '           </tr>' +
            '       </table>' +
            '   </div>' +

            /**
             * SOAP Secession
             */
            '   <tpl for="soap">' +
            '       <div class="secession">' +
            '           <div class="title"> ' + _('soap') + ' </div>' +
            '           <p><span>' + _('subjective') + ':</span> {[this.doHtmlDecode(values.subjective) || "-"]} </p>' +
            '           <p><span>' + _('objective') + ':</span> {[this.doHtmlDecode(values.objective) || "-"]}</p>' +
            '           <p><span>' + _('assessment') + ':</span> {[this.doHtmlDecode(values.assessment) || "-"]}</p>' +
            '           <p><span>' + _('plan') + ':</span> {[this.doHtmlDecode(values.instructions) || "-"]}</p>' +
            '       </div>' +
            '   </tpl>' +
            /**
             * Speech Dictation Secession
             */
            '   <tpl for="speechdictation">' +
            '       <div class="secession">' +
            '           <div class="title"> ' + _('speech_dictation') + ' </div>' +
            '           <p><span>' + _('dictation') + ':</span> {dictation}</p>' +
            '           <p><span>' + _('additional_notes') + ':</span> {additional_notes}</p>' +
            '       </div>' +
            '   </tpl>' +
            '</div>',
            {

	            isNotNull: function(value){
	                return value != 'null' && value != null;
	            },

		        isNotEmpty:function(value){

	            },

		        getVitalsValue:function(value){
			        return (value == 0 || value == null) ? '-' : value;
		        },

	            isMetric:function(){
		            return g('units_of_measurement') == 'metric';
	            },

	            doHtmlDecode:function(v){
		            return Ext.String.htmlDecode(v);
	            }



            }
        );

		me.callParent(arguments);
	}

});
