jQuery(document).ready(function($){
	
	var submitButton = document.getElementById('usersubmit');
	
	var ajaxFunctionformprocess = function(fromdata, action){
		$.ajax({
			type:'post',
			url: usersubmitform.url,
			data:{
				action:action,
				data:fromdata,
				security:usersubmitform.security,
				
				
				
			},
			success:function(reponse){
				$('div.msg').html(reponse);
			},
			error:function(response){
				alert(response);
			}
			
			
		});
		
		
		
	}
	
	submitButton.addEventListener('click', function(event){
		event.preventDefault();
		var fromdata = {
			'name':document.getElementById('username').value,
			'email':document.getElementById('useremail').value,
			'option':document.getElementById('useroption').value,
			'content':document.getElementById('usercontent').value,
			
			
		};
		ajaxFunctionformprocess(fromdata, 'form_action_function');	
			
		});
		
	
	
	
});