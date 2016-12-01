$(document).ready(function(){
    if ($('.cnt-AddComponentControl').length) {
        $('#search-component').keyup(function() {
            var searchText = $(this).val();

            $('.component-list li a').each(function(){
                var currentLiText = $(this).text().toUpperCase();
                var showCurrentLi = currentLiText.indexOf(searchText.toUpperCase()) !== -1;

                $(this).closest('li').toggle(showCurrentLi);
            });     
        });
        
        if ($('.component-content[data-link]') !== null) {
            var that = $('.component-content');
            var link = that.data('link');

            if (link) {
                that.removeData('link');
                that.html(loader());
                
                $.nette.ajax({
                    url: link,
                    unique: false,
                    headers: {
                        'X-Component': true
                    },
                    success: function(data) {
                        that.html(data);
                    },
                    error: function(error) {
                        console.log(error);

                        that.html(error.statusText);
                    }
                }).done(function() {
                    WAME.init(that);
                    initForm(that);
                });
            }
        }
    }
});

var initForm = function(object) {
    if (object.find('form').length) {
        object.find('form').each(function( index, form ) {
            Nette.initForm(form);
        });
    }
};

var componentPushpin = function() {
    var wrapperHeight = $('.cnt-AddComponentControl .component-info .card-panel .wrapper').outerHeight();
    var windowHeight = $(window).outerHeight() - $('.page-content').offset().top;
    
    if (wrapperHeight < windowHeight) {
        $('.cnt-AddComponentControl .component-info .card-panel .wrapper').pushpin({ 
            top: $('.cnt-AddComponentControl .component-info .card-panel').offset().top,
            bottom: $('footer').offset().top - $('.cnt-AddComponentControl .component-info .card-panel .wrapper').outerHeight(true) - 20,
            offset: 70
        });
    } else {

    }
};

/**
 * Loader
 *
 * @returns {String}
 */
var loader = function() {
    var $loader = $('<div/>', {
        'class': 'preloader valign-wrapper',
        'html': $('<div/>', {
            'class': 'preloader-wrapper active valign',
            'html': $('<div/>', {
                'class': 'spinner-layer',
                'html': '<div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div>'
            })
        })
    });

    return $loader;
};