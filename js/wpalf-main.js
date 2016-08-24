/**

WP AJAX Login Form


*/

jQuery(document).ready(function($) {

 	var $formLogin = $('#wpalf-login-form');
    var $formLost = $('#wpalf-lost-form');
    var $formRegister = $('#wpalf-register-form');
    var $divForms = $('#wpalf-div-forms');
    var $modalAnimateTime = 300;
    var $msgAnimateTime = 150;
    var $msgShowTime = 2000;

	// Show the login dialog box on click

    $('#wpalf-btn').on('click', function(e){
        $('body').prepend('<div class="login_overlay"></div>');
        $('#wpalf-login-modal').fadeIn(500);
        $('#wpalf-username').focus();
        $('div.login_overlay, #wpalf-login-modal button.close').on('click', function(){
            $('div.login_overlay').remove();
            $('#wpalf-login-modal').hide();            
        });
        e.preventDefault();
    });

	function modalAnimate ($oldForm, $newForm) {
	        var $oldH = $oldForm.height();
	        var $newH = $newForm.height();
	        $divForms.css("height",$oldH);
	        $oldForm.fadeToggle($modalAnimateTime, function(){
	            $divForms.animate({height: $newH}, $modalAnimateTime, function(){
	                $newForm.fadeToggle($modalAnimateTime);
	            });
	        });
	}


	$('#login_register_btn').click( function () { 
        // grecaptcha.render('g-recaptcha-reg', {'sitekey': wpalf_login_object.gsitekey});
        modalAnimate($formLogin, $formRegister) 
    });
    $('#register_login_btn').click( function () { modalAnimate($formRegister, $formLogin); });
    $('#login_lost_btn').click( function () { modalAnimate($formLogin, $formLost); });
    $('#lost_login_btn').click( function () { modalAnimate($formLost, $formLogin); });
    $('#lost_register_btn').click( function () { modalAnimate($formLost, $formRegister); });
    $('#register_lost_btn').click( function () { modalAnimate($formRegister, $formLost); });


    $('#wpalf-div-forms form').submit(function(e) {
    	switch (this.id) {
    		case 'wpalf-login-form':
    			$('#wpalf-div-forms p.status').show().text(wpalf_login_object.loadingmessage);
                $('#wpalf-div-forms p.status').css('color', 'black');
    			$.ajax({
    				type: 'POST',
    				dataType: 'json',
    				url: wpalf_login_object.ajaxurl,
    				data: {
					    'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
					    'username': $('#wpalf-login-form #wpalf-username').val(), 
					    'password': $('#wpalf-login-form #wpalf-password').val(), 
					    'security': $('#wpalf-login-form #security').val(),
                        'g-recaptcha-response': $('#g-recaptcha-response').val()
					},
					success: function(data) {
						$('#wpalf-div-forms p.status').text(data.message);
                        if (data.code == 503) {
                            $('#wpalf-login-form #wpalf-password').val('').change();
                            $('#wpalf-login-form #wpalf-username').focus();
                            $('#wpalf-div-forms p.status').css('color', 'red');
                            grecaptcha.reset();
                        }
                        if (data.code == 200) { 
                            $('#wpalf-div-forms p.status').css('color', 'green');
                        }
                		if (data.loggedin == true){
                    		document.location.href = wpalf_login_object.redirecturl;
                		}
					},
                    error: function() {
                        $('#wpalf-div-forms p.status').text('Ошибка на сервере');
                        $('#wpalf-div-forms p.status').css('color', 'red');
                    }

    			});
    			return false;
    			break;
    		case 'wpalf-lost-form':
    			alert('LOST form');
    			return false;
    			break;	
    		case 'wpalf-register-form':
                $('#wpalf-div-forms p.status').show().text(wpalf_login_object.loadingmessage);
                $('#wpalf-div-forms p.status').css('color', 'black');
                alert($('#wpalf-register-form #security').val());
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: wpalf_login_object.ajaxurl,
                    data: {
                        'action': 'ajaxregister', //calls wp_ajax_nopriv_ajaxregister
                        'username': $('#wpalf-register-form #register_username').val(), 
                        'email'   : $('#wpalf-register-form #register_email'),
                        'security': $('#wpalf-register-form #security').val(),
                        'g-recaptcha-response': $('#g-recaptcha-response-2').val()
                    },
                    success: function(data) {
                        $('#wpalf-div-forms p.status').text(data.message);
/*                        if (data.code == 503) {
                            $('#wpalf-login-form #wpalf-password').val('').change();
                            $('#wpalf-login-form #wpalf-username').focus();
                            $('#wpalf-div-forms p.status').css('color', 'red');
                            grecaptcha.reset();
                        }  */
                        if (data.code == 200) { 
                            $('#wpalf-div-forms p.status').css('color', 'green');
                        }
                        // if (data.loggedin == true){
                        //     document.location.href = wpalf_login_object.redirecturl;
                        // }
                    },
                    error: function() {
                        $('#wpalf-div-forms p.status').text('Ошибка на сервере');
                        $('#wpalf-div-forms p.status').css('color', 'red');
                    }

                });
    			return false;
    			break;	
    			
    	}
    	e.preventDefault();
    });


});


var onloadCallback = function() {
    mysitekey = wpalf_login_object.gsitekey;
    grecaptcha.render('g-recaptcha-login', {
        'sitekey' : mysitekey
    });

    grecaptcha.render('g-recaptcha-lost', {
        'sitekey' : mysitekey
    });

    grecaptcha.render('g-recaptcha-reg', {
        'sitekey' : mysitekey
    });


};





