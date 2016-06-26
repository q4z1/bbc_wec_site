$(window).load
(
	function()
	{
		if($('button#udef').length > 0){
			$('button#udef').click(function(e){
				e.preventDefault();
				$('button#udef').removeClass('btn-primary');
				$('button#udef').addClass('btn-warning');
				$('button#udef').html(
					'<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Upoading...'
				);
				$.post(
					"/ajax/upload/def/",
					{
						step: $('#step').val(),
						logurl: $('#logurl').val()
					},
					function(data) {
            showModal(data);
						window.setTimeout(function(){
							window.location.href="/main/results/game/"+$('#game_id').val()+"/";},
							2000
						);
          }
				);
			});
		}
	}
);

