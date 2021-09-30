<div id='editmod' class='modal'>
		<div id='editmodbg' class="modal-background"><script>
			$('#editmodbg').on('click',function(){
				$('#editmod').removeClass('is-active');
			});
		</script>
		</div>
		<div class='modal-card'>
		<header class='modal-card-head'>
			<p class="modal-card-title">Edit Item</p>
		</header>
		<section class='modal-card-body'>
			<form id='editItem'></form>
			<input hidden name='id-edit'>
		</section>
		<footer class='modal-card-foot'>
			<button id='EditSave' class='button is-primary'>Salvar</button>
			<button class='button' onClick="$('#editmod').removeClass('is-active')">Cancel</button>
		</footer>
		</div>
	</div>