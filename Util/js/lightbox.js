$(function(){  
    // Lightbox
    self.defaultLightBoxId = '#lightBox';
   
    self.abrirLightBox = function()
    {
    	// START - SIMPLE MODAL JQUERY	
    	$('.window .close, #mask').click(function (e) {
    		$('.window').fadeOut('normal', function() {
    			$('#mask').fadeOut('normal');
    		});
    		e.preventDefault();
    	});		


        var id = defaultLightBoxId;
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        $('#mask').css({
           'width':maskWidth,
           'height':maskHeight,
           'background' : '#000',
           'opacity' : 0.8,
           'cursor' : 'pointer'
        });


        $('#mask').fadeIn();

        //Get the window height and width
        var winH = $(id).height();
        var winW = $(id).width();

       // $(id).css('top',  -$(id).height()/2);
        //$(id).css('left', -$(id).width()/2);

        $(id).fadeIn(1000);
    }

})

function fechaLightBox( classWindow)
{
	$('.'+classWindow).fadeOut('normal', function() {
		$('#mask').fadeOut('normal');
	});
}