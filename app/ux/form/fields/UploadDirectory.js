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

Ext.define('App.ux.form.fields.UploadDirectory', {
	extend: 'Ext.form.field.Base',

	xtype: 'uploaddirectoryfield',
	inputType: 'file',
	inputAttrTpl: 'webkitdirectory directory',
	paths: [],

	onRender: function() {
		var me = this;
		me.callParent(arguments);
		me.inputEl.dom.onchange = function () {
			var files = this.files;
			me.paths = [];
			for (var i = 0; i < files.length; i++) {
				Ext.Array.push(me.paths,files[i].webkitRelativePath);
			}
		}
	},

	isFileUpload: function() {
		return true;
	},

	restoreInput: function (el) {
		this.inputEl.remove();
		el = Ext.get(el);
		this.inputEl = this.el.appendChild(el);
		this.inputEl.dom.value = '';
		return this.inputEl;
	},

	extractFileInput: function() {
		return this.inputEl.dom;
	}

});