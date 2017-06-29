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

Ext.define('App.view.administration.Backup', {
    extend: 'App.ux.RenderPanel',
    itemId: 'AdministrationBackupPanel',
    pageTitle: _('backup'),

    initComponent: function(){
        var me = this;

        // *************************************************************************************
        // Module Data Store
        // *************************************************************************************
        me.store = Ext.create('App.store.administration.BackUps');

        me.pageBody = [
	        {
	        	xtype: 'grid',
		        frame: true,
		        store: me.store,
		        itemId: 'AdministrationBackupGrid',
		        tbar: [
			        {
			        	xtype: 'button',
				        text: _('backup_now'),
				        itemId: 'AdministrationBackupAsyncBackupBtn'
			        },
			        '->',
			        {
			        	xtype: 'button',
				        text: _('refresh'),
				        itemId: 'AdministrationBackupRefreshBtn'
			        }
		        ],
		        columns: [
			        {
				        xtype: 'datecolumn',
				        text: _('date'),
				        flex: 1,
				        dataIndex: 'filemtime',
				        format: 'l, F j, Y, g:i a'
			        },
			        {
				        text: _('file'),
				        flex: 1,
				        dataIndex: 'filename'
			        },
			        {
				        text: _('size'),
				        flex: 1,
				        dataIndex: 'filesize'
			        }
		        ]
	        }
        ];

        me.callParent(arguments);
    }

});
