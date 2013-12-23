$(function(){
	/************** LIVE PHOTO UPLOAD */
	$('#postForm input[type="file"]').change(function(e){
		e.preventDefault();
		var file = this.files[0];
	    var fd = new FormData();
	    var ajaxupload =  $('.photo-file:first').attr('data-url');
	    var canonical =  $('.photo-file:first').attr('data-canonical');
	    fd.append(this.name, file);
	    if(this.name != ''){
	        var request = $.ajax({
	           url: ajaxupload,
	           type: "POST",
	           data: fd,
	           xhr: function() {  
	               myXhr = $.ajaxSettings.xhr();
	               if(myXhr.upload){ 
	                   myXhr.upload.addEventListener('progress',startProgress, false); // for handling the progress of the upload
	               }
	               return myXhr;
	           },
	           processData: false,
	           contentType: false,
	        });
	        var progressBar = $(this).parent().prev();
	        var progressText = progressBar.find('.percent b');
	        function startProgress(e){
	            if(e.lengthComputable){
	            	 var progressW = parseInt((e.loaded / e.total) * 100);
	            	 $('.progress-bar').width(progressW + "%");
	            }
	        };
	        request.done(function (response, textStatus, jqXHR){
	        	var jsonResponse = jQuery.parseJSON(response);
	            if(jsonResponse.success){
	            	progressBar.fadeOut(function(){
	            		$('.preview-img').before('<img class="preview" src="'+canonical+jsonResponse.fileUrl+'" alt=""/>');
	            	});
	            }
	        });
	        request.fail(function (jqXHR, textStatus, errorThrown){
	            console.error("The following error occured: "+ textStatus, errorThrown);
	        });
	    }
	});
    $('#postForm .trash').click(function(){
        var imgUp = $(this).parent().prev();
        var progressBar = $(this).parent().next();
        imgUp.fadeOut(function(){
            progressBar.fadeIn();
        });
    });
    $('textarea[name="paragraph"]').on('keyup', function (){
        var maxChar = 500;
        if($(this).val().length > maxChar) $(this).val($(this).val().slice(0, maxChar));
        $('.character-left').text(maxChar - $(this).val().length);
    })
});