Ext.define("App.ux.form.fields.plugin.Measurements", {
	extend : "Ext.AbstractPlugin",
	alias  : "plugin.measurements",

	init: function(cmp) {
		var me = this;

		cmp.on("blur", me.onFieldChange, me);
	},

	onFieldChange: function(field) {
		if(field.inputEl.dom.value.replace){
			field.inputEl.dom.value = field.inputEl.dom.value.replace(/(x)/g, ' $1 ').replace(/ +/g, ' ');
		}
	},

});