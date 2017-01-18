(function () {
	'use strict';

	var Zoom = Darkroom.Transformation.extend({
		applyTransformation: function(canvas, image, next) {

			console.log(this);
			console.log(canvas);
			console.log(image);
			console.log(next);

		}
	});


	Darkroom.plugins['zoom'] = Darkroom.Plugin.extend({

		defaults: {
			callback: function () {
				this.darkroom.selfDestroy();
			}
		},

		initialize: function InitializeDarkroomZoomPlugin() {

			if(this.darkroom.containerElement.addEventListener){
				// IE9, Chrome, Safari, Opera
				console.log('IE9, Chrome, Safari, Opera');
				this.darkroom.containerElement.addEventListener('mousewheel', this.zoom.bind(this), false);
				this.darkroom.containerElement.addEventListener('DOMMouseScroll', this.zoom.bind(this), false);
			}else{
				this.darkroom.containerElement.attachEvent("onmousewheel", this.zoom.bind(this));
			}

		},

		zoom: function (e) {
			var evt = window.event || e,
				delta = evt.detail ? evt.detail * (-120) : evt.wheelDelta,
				curZoom = this.darkroom.canvas.getZoom(),
				newZoom = curZoom + delta / 4000,
				x = e.offsetX,
				y = e.offsetY;

			this.darkroom.canvas.zoomToPoint({ x: x, y: y }, newZoom);

		},
	});

})();
