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

Ext.define('App.view.patient.encounter.DictationPanel', {
	extend: 'Ext.form.Panel',
	requires: [

	],
	action: 'patient.encounter.dicatation',
	itemId: 'dictationPanel',
	title: _('dictation'),
	layout: 'fit',
	frame: true,
	bodyPadding: 10,
	pid: null,
	eid: null,

	initComponent: function(){
		var me = this;


		me.items = [
			{
				xtype: 'textareafield'
			}

		];

		me.buttons =[
			{
				text: _('save'),
				iconCls: 'save',
				action: 'dicatationSave',
				scope: me,
				itemId: 'encounterRecordAdd',
				handler: me.onDictationSave
			}
		];

		me.callParent(arguments);

	},

	/**
	 *
	 * @param btn
	 */
	onDictationSave: function(btn){
		this.enc.onEncounterUpdate(btn)
	}
});
