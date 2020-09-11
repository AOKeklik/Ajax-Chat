	$(document).ready(function() {
	// toload comments file 
		$('#konusmalar').load('fonksiyon.php?chat=oku')
		
	// to refles comment lines
		setInterval(function(){
			$('#konusmalar').load('fonksiyon.php?chat=oku')
		},1000)
		
	// toenter new comment
		$('#gonder').keyup(function(e){
			var text = $('#gonder').val()
			var uzunluk = text.length
			var karakter = $('#gonder').attr('maxlength')
			
			if(e.keyCode == 13){
				if(uzunluk > 5 && uzunluk < karakter){
				   $.ajax({
					 	type: 'POST',
					   	url: 'fonksiyon.php?chat=ekle',
					   	data: $('#mesajgonder').serialize(),
						success: function(donenveri){
							$('#gonder').val('')
							$('#konusmalar').load('fonksiyon.php?chat=oku')
							$('#konusmalar').scrollTop($('#konusmalar')[0].scrollHeight)
				   		}
				   })
				}else{
				   $('#gonder').val('')
				}
			}
			
		})
		
    });