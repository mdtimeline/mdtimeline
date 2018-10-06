Ext.define('App.ux.window.TopazSignature', {
	extend: 'Ext.window.Window',
	xtype: 'topazsignaturewidown',

	requires: [
		'Ext.layout.container.Fit'
	],

	title: _('signature'),
	width: 400,
	height: 200,
	modal: true,
	draggable: true,
	resizable: false,
	buttons: [],
	layout: 'fit',
	items: [
		{
			xtype: 'component',
			autoEl: {
				tag: 'canvas',
				style: 'background-color:white'
			}
		}
	],

	canvasObj: undefined,
	imgWidth: undefined,
	imgHeight: undefined,
	signatureObj: undefined,
	extensionDataElement: undefined,

	initComponent: function () {

		var me = this;

		me.buttons = [
			{
				text: _('cancel'),
				handler: me.onCancelBtnClick,
				scope: me
			},
			{
				text: _('save'),
				handler: me.onSaveBtnClick,
				scope: me
			}
		];

		me.callParent(arguments);

	},

	onShow: function(){
		this.callParent(arguments);
		this.StartSign();
	},

	onDestroy: function(){
		this.callParent(arguments);
		this.StopSign();
	},

	StopSign: function(){
		var me = this;

		say('StopSign');
		say(me.getComponent(0));

		document.removeEventListener('SignResponse', this.SignResponse.bind(this), false);

		document.removeEventListener('SignResponse', function (event) {
			me.SignResponse(event);
		}, false);

		me.extensionDataElement.parentNode.removeChild(me.extensionDataElement);

	},

	StartSign: function () {
		say('StartSign');

		var me = this;

		me.canvasObj = me.getComponent(0).el.dom;
		me.imgWidth = me.canvasObj.width;
		me.imgHeight = me.canvasObj.height;
		me.canvasObj.getContext('2d').clearRect(0, 0, me.canvasObj.width, me.canvasObj.height);

		var message = {
			"firstName": "",
			"lastName": "",
			"eMail": "",
			"location": "",
			"imageFormat": 1,
			"imageX": me.imgWidth,
			"imageY": me.imgHeight,
			"imageTransparency": false,
			"imageScaling": false,
			"maxUpScalePercent": 0.0,
			"rawDataFormat": "ENC",
			"minSigPoints": 25
		};

		document.addEventListener('SignResponse', function (event) {
			me.SignResponse(event);
		}, false);

		var messageData = JSON.stringify(message);

		me.extensionDataElement = document.createElement("MyExtensionDataElement");
		me.extensionDataElement.setAttribute("messageAttribute", messageData);

		document.documentElement.appendChild(me.extensionDataElement);
		var evt = document.createEvent("Events");
		evt.initEvent("SignStartEvent", true, false);
		me.extensionDataElement.dispatchEvent(evt);

	},

	SignResponse: function (event) {
		say('SignResponse');
		say(event);

		var me = this,
			str = event.target.getAttribute("msgAttribute"),
			obj = JSON.parse(str);

		this.SetValues(obj, me.imgWidth, me.imgHeight);
	},

	SetValues: function (objResponse, imageWidth, imageHeight) {
		say('SetValues');
		say(objResponse);
		say(imageWidth);
		say(imageHeight);

		var me = this,
			obj = JSON.parse(JSON.stringify(objResponse)),
			ctx = me.canvasObj.getContext('2d');

		if (obj.errorMsg != null && obj.errorMsg != "" && obj.errorMsg != "undefined") {
			alert(obj.errorMsg);
		}
		else {
			if (obj.isSigned) {
				// document.FORM1.sigRawData.value += obj.imageData;
				// document.FORM1.sigStringData.value += obj.sigString;

				me.signatureObj = obj;

				var img = new Image();
				img.onload = function () {
					ctx.drawImage(img, 0, 0, imageWidth, imageHeight);
				};

				img.src = "data:image/png;base64," + obj.imageData;
			}
		}
	},

	onSaveBtnClick: function () {
		this.fireEvent('signaturesave', this, this.signatureObj);
		this.close();
	},

	onCancelBtnClick: function () {
		this.close();
	}
});