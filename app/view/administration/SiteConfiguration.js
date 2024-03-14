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

Ext.define('App.view.administration.SiteConfiguration', {
	extend:'App.ux.RenderPanel',
	pageTitle:_('site_configuration'),
	pagePadding: 0,
	itemId: 'ServerConfiguration',
	bodyPadding: 0,
	initComponent:function(){
		var me = this;

		me.pageBody = [
			{
				xtype: 'miframe',
				padding: 0,
				margin: 0,
				src: ('lib/AceEditor/editor.html?_dc=' + new Date().getTime()),
				itemId: 'ServerConfigurationIFrame'
			}
		];
		me.pageBBar = [
			'->',
			{
				xtype: 'button',
				text: _('reset'),
				width: 70,
				itemId: 'ServerConfigurationResetBtn'
			},
			'-',
			{
				xtype: 'button',
				text: _('save'),
				width: 70,
				itemId: 'ServerConfigurationSaveBtn'
			}
		];

		me.callParent(arguments);
	}

});
