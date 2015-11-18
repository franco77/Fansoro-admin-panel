
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
        },

        /**
        *
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
        }
    };
})();

app.init();

// =====================================
// Get Date
// =====================================
var date = new Date(),
    year = date.getFullYear(),
    month = date.getMonth(),
    day = date.getUTCDate(),
    months = [ "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];

document.getElementById('daymonthyear').innerHTML = day + " " + months[month] + " " + year;

// =====================================
// Get Time
// =====================================

function addZero(i) {
// This checks to see if the number is below 10 and then prepends a '0' - clever shit :P
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

function newTime() {
    var d = new Date();
    var h = addZero(d.getHours());
    var m = addZero(d.getMinutes());
    var s = addZero(d.getSeconds());
    var x = document.getElementById("hourminutesecond");

    x.innerHTML = h + " : " + m + " : " + s;
}

newTime();
setInterval(newTime, 1000);

/*
Please consider that the JS part isn't production ready at all, I just code it to show the concept of merging filters and titles together !
*/
$(document).ready(function(){
    $('.filterable .btn-filter').click(function(){
        var $panel = $(this).parents('.filterable'),
        $filters = $panel.find('.filters input'),
        $tbody = $panel.find('.table tbody');
        if ($filters.prop('disabled') == true) {
            $filters.prop('disabled', false);
            $filters.first().focus();
        } else {
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();
        }
    });

    $('.filterable .filters input').keyup(function(e){
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9') return;
        /* Useful DOM data and selectors */
        var $input = $(this),
        inputContent = $input.val().toLowerCase(),
        $panel = $input.parents('.filterable'),
        column = $panel.find('.filters th').index($input.parents('th')),
        $table = $panel.find('.table'),
        $rows = $table.find('tbody tr');
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function(){
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
        $rows.show();
        $filteredRows.hide();
        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }
    });
});

/*
Please consider that the JS part isn't production ready at all, I just code it to show the concept of merging filters and titles together !
*/
$(document).ready(function(){
    $('.filterable .btn-filter').click(function(){
        var $panel = $(this).parents('.filterable'),
        $filters = $panel.find('.filters input'),
        $tbody = $panel.find('.table tbody');
        if ($filters.prop('disabled') == true) {
            $filters.prop('disabled', false);
            $filters.first().focus();
        } else {
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();
        }
    });

    $('.filterable .filters input').keyup(function(e){
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9') return;
        /* Useful DOM data and selectors */
        var $input = $(this),
        inputContent = $input.val().toLowerCase(),
        $panel = $input.parents('.filterable'),
        column = $panel.find('.filters th').index($input.parents('th')),
        $table = $panel.find('.table'),
        $rows = $table.find('tbody tr');
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function(){
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
        $rows.show();
        $filteredRows.hide();
        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }
    });
});

