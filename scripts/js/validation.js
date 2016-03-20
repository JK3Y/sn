$(document).ready(function() {
// Validation for the Login form on the home page
	$("#loginForm").submit(function(event) {
		event.preventDefault();
		$.ajax({
			method: 'POST',
			url: 'scripts/php/login.php',
			data: $('#loginForm').serialize(), //create data object out of form values
			dataType: 'json',
			encode: true,
			beforeSend: function(){
				$("#btnLogin").val('Logging in...');
			}
		})
		.done(function(data) {
			console.log(data);
			if (data.success == true) {
				location.href = "myhome.php";
			}
			else {
				alert('login failed');
				$('#errorList').empty();
				$('#errorList').append('<li><em>' + data.errors +'</em></li>');
				$('#errorContainer').show();
				$('#errorList').show();
				$("#btnLogin").val('Login');
			}
		})
		.fail(function(data) {
			console.log(data);
			alert('An unknown error occured. Please try again.');
			$("#btnLogin").val('Login');
		});
		
	});


// Validation for the Registration form on the home page.
	$("#registerForm").submit(function(event) {
		event.preventDefault();
	}).validate({
			debug: true,
			// 	SPECIFY VALIDATION RULES
			rules: {
				username: {
					required: true,
					minlength: 3
				},
				password: {
					minlength: 5,
					equalTo: '#confirmpass',
					required: true
				},
				confirmpass: {
					minlength: 5,
					equalTo: '#password',
					required: true
				},
				email: {
					email: true,
					required: true
				}
			},
			// MESSAGES FOR WHEN THE VALIDATION FAILS
			messages: {
				username: {
					required: "Please enter a username.",
					minlength: "Username must be at least 3 characters."
				},
				password: {
					required: "Please enter a password.",
					minlength: "Password must be at least 5 characters.",
					equalTo: "Password and confirmation do not match."
				},
				confirmpass: {
					required: "Please confirm password.",
					equalTo: "Password and confirmation do not match."
				},
				email: {
					required: "Email is required."
				}
			},
			// ERROR DISPLAY OPTIONS
			errorContainer: "#errorContainer",
			errorLabelContainer: "#errorList",
			errorElement: "li",
			wrapper: "em",

			submitHandler: function(form) {
				$.ajax({
					method: 'POST',
					url: 'scripts/php/register.php',
					data: $('#registerForm').serialize(), //create data object out of form values
					dataType: 'json',
					encode: true,
					cache: false,
					beforeSend: function() {
						$("#btnRegister").val('Processing...');
					}
				})
				.done(function(data) {
					console.log(data);
					console.log($('#registerForm').serialize());
					if (data.success == true) {
						$('#formWrapper').hide();
						$('#errorContainer').hide();
						$('#errorList').hide();
						$('#regConfirm').show();
						$('#registerForm').each(function() {
							this.reset();
						});
						$('#btnRegister').val('Register');
					}
					else {
						$('#errorList').empty();
						$('#errorList').prepend('<li><em>' + data.errors +'</em></li>');
						$('#errorContainer').show();
						$('#errorList').show();
					}
				})
				.fail(function(data) {
					console.log(data);
					alert('An unknown error occured. Please try again.');
					$("#btnRegister").val('Register');
				});
			}
		});


// Validation for the Update form on Account.php
	$("#updateForm").submit(function(event) {
		event.preventDefault();
	}).validate({
			debug: true,
			// 	SPECIFY VALIDATION RULES
			rules: {
				currentpass: {
					required: true,
					minlength: 5
				},
				newpassword: {
					minlength: 5,
					equalTo: '#newpassword',
					required: {
						depends: function() {
							return ($("newpassword2").is(':filled'));
						}
					}
				},
				newpassword2: {
					minlength: 5,
					equalTo: '#newpassword',
					required: {
						depends: function() {
							return ($("newpassword").is(':filled'));
						}
					}
				},
				email: {
					email: true
				}
			},
			// MESSAGES FOR WHEN THE VALIDATION FAILS
			messages: {
				currentpass: {
					required: "Please enter your current password.",
					minlength: "Current password is at least 5 characters."
				},
				newpassword: {
					minlength: "New password must be at least 5 characters.",
					equalTo: "Password and confirmation do not match."
				},
				newpassword2: {
					minlength: "New password confirmation must be at least 5 characters.",
					equalTo: "Password and confirmation do not match."
				}
			},
			// ERROR DISPLAY OPTIONS
			errorContainer: "#errorContainer",
			errorLabelContainer: "#errorList",
			errorElement: "li",
			wrapper: "em",

			submitHandler: function(form) {
				$.ajax({
					method: 'POST',
					url: 'scripts/php/update.php',
					data: $('#updateForm').serialize(), //create data object out of form values
					dataType: 'json',
					encode: true,
				})
				.done(function(data) {
					console.log(data);
					if (data.success == true) {
						$('#updated').show();
						$('#errorContainer').hide();
						$('#errorList').hide();
					}
					else {
						$('#updated').hide();
						$('#errorList').empty();
						$('#errorList').prepend('<li><em>' + data.errors +'</em></li>');
						$('#errorContainer').show();
						$('#errorList').show();
					}
				})
				.fail(function(data) {
					console.log(data);
					alert('An unknown error occured. Please try again.');
				});
			}
		});

});