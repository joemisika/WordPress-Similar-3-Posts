jQuery(document).ready(function($) {
    	$('#similar3').on('click', function(e){
        		e.preventDefault();
        		var postData = {
        			action: 'getRelated',
        			postid: relatedobject.postid,
        			category: relatedobject.category
        		}
        		$.ajax({
            		cache: false,
            		url: relatedobject.ajax_url,
            		type: "post",
            		dataType: "json",
            		data: postData,
            		beforeSend: function() {
                    		$( '#related-articles' ).html( 'Loading...' );
                		},
                		success:function(data){
                			$('#related-articles').html(data.result);
            		},
            		error:function(data){
                			$('#related-articles').html('Sorry, looks like this article does not have related posts');
            		}
        		});
        		return false;
    	});
});