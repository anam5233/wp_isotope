;
var page = 1;
(function($){
	$(document).ready(function(){
		$("#grid").imagesLoaded(function(){
			$("#grid").isotope({
				itemSelector:".grid-item"
			});
		});

		$(".filter").on('click', function(){
			var filter = $(this).data("filter");
			if(filter != '*') filter = "."+filter;
			$("#grid").isotope({
				filter:filter
			});
		});



	});

})(jQuery);