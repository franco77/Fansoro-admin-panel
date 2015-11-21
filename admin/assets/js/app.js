
var app = (function() {

	'use-strict';


	/**
	 *    Description:
	 *    Private functions goes here
	 */
	function _(element) {
		return document.querySelector(element);
	}


	return {

		/**
		 *    Description:
		 *    Start javascript
		 *
		 *    Syntax:
		 *    app.init();
		 */
		init: function() {
			var self = this;
			// progress functions
			this.progress(document.body,function(num, span, wait){
				// remove loader
				if(num > 100){
					span.remove();
					clearTimeout(wait);
					self.loadFunctions();
				}
			});
		},

		/**
		 *    Description:
		 *    Load functions on init
		 *
		 *    Syntax:
		 *    app.loadFunctions();
		 */
		loadFunctions: function(){
			// search
			this.searchForm();
			// remplace input submit value on submit form
			if(_('form')) {
				// all submit imputs
				_('form').addEventListener('submit',function(){
					_('input[type="submit"]').value = 'Send data...';
				});
				// login form
				if(_('.form-signin')){
					 _('.form-signin').addEventListener('submit',function(){
						_('input[type="submit"]').value = 'Check Login...';
					});
				 }
			}
			// image preview
			this.media('#image-input','#image-display');
			this.media('#media-input','#media-display');
			// clock
			this.getClock();
		},

		/**
		*    Description:
		*   Search pages ,blocks and uploads
		*
		*/
		searchForm: function(){
			// search pages on enter
			if(_('#search')){
				_('#search').addEventListener('submit',function(event){
					event.preventDefault();
					window.location.href= [
					   root, // site url
					   '/action/search/', // action
					   _('#searchthis').getAttribute('data-search'), // get data-search
					   '/',
					   _('#searchthis').value // value
					].join('');
				});
			}
			if(_('#search-files')){
				// search files on enter
				_('#search-files').addEventListener('submit',function(event){
					event.preventDefault();
					window.location.href= [
						root, // site url
						'/action/searchfiles/',
						_('#searchthis').value // value
					].join('');
				});
			}

			if(_('#search-media')){
				// search files on enter
				_('#search-media').addEventListener('submit',function(event){
					event.preventDefault();
					window.location.href= [
						root, // site url
						'/action/searchmedia/',
						_('#searchthis').value // value
					].join('');
				});
			}

			if(_('#searchinthemes')){
				// search files on enter
				_('#searchinthemes').addEventListener('submit',function(event){
					event.preventDefault();
					window.location.href= [
						root, // site url
						'/action/searchinthemes/',
						_('#searchthis').value // value
					].join('');
				});
			}
		},
				/**
		 * media preview function javascript
		 * use for input file and display in div
		 * panel.media(el,display);
		 *
		 * @param el
		 * @param callback function
		 * @return callback
		 */
		media: function(el, display) {
			// demo img
			var imageDisplay = document.querySelector(display);
			if (imageDisplay) {
				var demo = imageDisplay.getAttribute('src'),
					database = window.localStorage;
				// clear first
				database.setItem("image-base64", '');
				if (!database.getItem("image-base64")) {
					var t = setTimeout(function() {
						database.setItem("image-base64", demo);
						clearTimeout(t);
					}, 100);
				}

				/** @type {Node} */
				var imgInput = document.querySelector(el),
					/** @type {Node} */
					imgContainer = document.querySelector(display),
					/** Restore image src from local storage */
					updateUi = function() {
						var t2 = setTimeout(function() {
							imgContainer.src = database.getItem("image-base64");
							clearTimeout(t2);
						}, 200);
					},
					/** Register event listeners */
					bindUi = function() {
						imgInput.addEventListener("change", function() {
							if (this.files.length) {
								var reader = new FileReader();
								reader.onload = function(e) {
									database.setItem("image-base64",e.target.result);
									updateUi();
								};
								reader.readAsDataURL(this.files[0]);
							}
						}, false);
					};
				updateUi();
				bindUi();
			}
		},
				/**
		 * progress bar function javascript
		 * panel.progress(el,function(num,span,interval){
		 *  // callback goes here
		 *  if(num === 100){
		 *    clearInterval(interval);
		 *    span.remove();
		 *  }
		 * });
		 *
		 * @param el
		 * @param callback function(num,span,inteval)
		 * @return callback
		 */
		progress: function(el, callback) {
			var span = document.createElement("span"),a = 0;
				span.className = "loader-bar";
				el.appendChild(span);
			var interval = setInterval(function() {
				if (/loaded|complete/.test(document.readyState)) {
					var num = 10 * a++;
					return span.style.width = num + "%",callback ? callback(num, span, interval) : void 0;
				}
			}, 10);
		},
		
		/**
		 * Description
		 * Show date and time in header
		 * /
		 */
		getClock: function(){
			var date = new Date(),
				year = date.getFullYear(),
				month = date.getMonth(),
				day = date.getUTCDate(),
				months = [ "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];
			_('#daymonthyear').innerHTML = day + " " + months[month] + " " + year;
			this.newTime();
			setInterval(this.newTime, 1000);
		},
		
		
		/**
		 * Description
		 * This checks to see if the number is below 10 and then prepends a '0' - clever shit :P
		 */
		addZero: function(i){
			if (i < 10)  i = "0" + i;
			return i;
		},
		
		
		/**
		 * Description
		 * Get clock function
		 */
		newTime: function(){
			var d = new Date();
			var h = app.addZero(d.getHours());
			var m = app.addZero(d.getMinutes());
			var s = app.addZero(d.getSeconds());
			var x = _('#hourminutesecond');
			x.innerHTML = h + " : " + m + " : " + s;
		}
	};
})();

app.init();

