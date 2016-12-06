Ext.define('App.controller.BrowserHelper', {
	extend: 'Ext.app.Controller',
	requires: [],
	refs: [

	],
	//extensionId: '',    // Production extension
	extensionId: 'oamecjbhloegpbmafnhjbablcogdhpah',    // Delopement extension

	sendMessage: function (message, callback) {
		if (chrome && chrome.runtime) {
			chrome.runtime.sendMessage(this.extensionId, message,
				function (response) {
					if (typeof callback == 'function') callback(response);
				}
			);
		}
	}
});