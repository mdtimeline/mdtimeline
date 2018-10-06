/*!
 * Ext.ux.RatingField
 *
 * Copyright 2011, Dan Harabagiu
 * Licenced under the Apache License Version 2.0
 * See LICENSE
 *
 *
 * Version : 0.1 - Initial coding
 * Version : 0.2
 *  - Added Field reset button
 *  - Added CSS class for reset button
 *  - Added reset function for the field
 *  - Minimum number of stars is 2
 *  - On creation default value is now 0, was null
 *  - Option to choose left / right for the reset button position
 */
/*global Ext : false, */
Ext.define('App.ux.TopazSignature', {
	extend: 'Ext.Img',
	xtype: 'topazsignature',

	signatureObj: undefined,
	extensionDataElement: undefined,
	signatureEvt: undefined,

	style: 'background-color:white;border:solid 1px #1a9bfc',

	firstName: '',
	lastName: '',
	eMail: '',

	initComponent: function () {

		var me = this;

		me.callParent();
		me.on('afterrender', function () {

			Ext.Function.defer(function () {

				Ext.create('Ext.tip.ToolTip', {
					target: me.imgEl,
					html: 'Double Click to Capture Signature',
					showDelay: 10,
					hideDelay: 10,
					trackMouse: true
				});

			}, 250, me);

			me.el.on('dblclick', me.StartSignature, me);

		}, me);



	},

	onDestroy: function () {
		delete this.signatureObj;
		delete this.extensionDataElement;
		delete this.signatureEvt;
		this.callParent();
	},

	StartSignature: function(){

		if(this.fireEvent('beforesignature', this) === false) return;

		var me = this,
			message = {
				firstName: me.firstName || '',
				lastName: me.lastName || '',
				eMail: me.email || '',
				location: "",
				imageFormat: 1,
				imageX: me.getWidth(),
				imageY: me.getHeight(),
				imageTransparency: false,
				imageScaling: false,
				maxUpScalePercent: 0.0,
				rawDataFormat: "ENC",
				minSigPoints: 25
				// "displayCustomTextDetails": 1,
				// "customTextPercent": 50,
				// "customTextLine1": 'Text One',
				// "customTextLine2": 'Text Two',
			};

		document.addEventListener('SignResponse', function (event) {
			me.SignResponse(event);
		}, false);

		var messageData = JSON.stringify(message);

		me.imgEl.dom.setAttribute("messageAttribute", messageData);
		me.signatureEvt = document.createEvent("Events");
		me.signatureEvt.initEvent("SignStartEvent", true, false);
		me.imgEl.dom.dispatchEvent(me.signatureEvt);
	},

	SignResponse: function (event) {
		var me = this,
			str = event.target.getAttribute("msgAttribute"),
			obj = JSON.parse(str);

		this.SetValues(obj, me.getWidth(), me.getHeight());
	},

	SetValues: function (objResponse, imageWidth, imageHeight) {

		var me = this,
			obj = JSON.parse(JSON.stringify(objResponse));

		if (obj.errorMsg != null && obj.errorMsg != "" && obj.errorMsg != "undefined") {
			app.msg(_('oops'), obj.errorMsg, true);
		}
		else {
			if (obj.isSigned) {

				me.signatureObj = obj;
				me.setSrc("data:image/png;base64," + obj.imageData);

				if(this.fireEvent('signature', me, me.signatureObj) === false) return;

			}
		}
	}
});