/**
 * Created with IntelliJ IDEA.
 * User: ernesto
 * Date: 11/4/13
 * Time: 6:28 PM
 * To change this template use File | Settings | File Templates.
 */
Ext.define('App.model.administration.ReviewOfSystemSettings', {
	extend: 'Ext.data.Model',
	table: {
		name: 'review_of_system_settings'
	},
	fields: [
		{
			name: 'id',
			type: 'int'
		},
		{
			name: 'user_id',
			type: 'int',
			index: true
		},
		{
			name: 'settings_data',
			type: 'array'
		}
	],
	proxy: {
		type: 'direct',
		api: {
			read: 'Encounter.getReviewOfSystemSettingsByUserId',
			create: 'Encounter.saveReviewOfSystemSettings',
			update: 'Encounter.saveReviewOfSystemSettings'
		},
		remoteGroup: false
	}
});
