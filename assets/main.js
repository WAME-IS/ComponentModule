$(document).ready(function(){
    $('#search-component').keyup(function() {
        var searchText = $(this).val();

        $('.component-list li a').each(function(){
            var currentLiText = $(this).text().toUpperCase();
            var showCurrentLi = currentLiText.indexOf(searchText.toUpperCase()) !== -1;

            $(this).closest('li').toggle(showCurrentLi);
        });     
    });
    
    
    $('.cnt-AddComponentControl .component-info .card-panel .wrapper').pushpin({ 
        top: $('.cnt-AddComponentControl .component-info .card-panel').offset().top,
        bottom: $('footer').offset().top - $('.cnt-AddComponentControl .component-info .card-panel .wrapper').outerHeight(true) - 20,
        offset: 70
    });
});