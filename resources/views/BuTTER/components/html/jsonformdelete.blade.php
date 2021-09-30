<div id='delmod' class='modal'>
        <div id='delmodbg' class="modal-background"><script>
            $('#delmodbg').on('click',function(){
                $('#delmod').removeClass('is-active');
            });
        </script>
        </div>
    <input hidden name='id-del'>
    <div class='modal-card'>
        <header class='modal-card-head'>
            <p class='modal-card-title'>Delete Item?</p>
        </header>
        <footer class='modal-card-foot'>
            <button class='button is-danger' id='delConfirm'>Delete</button> 
            <button class='button' id='delBack' onClick="$('#delmod').removeClass('is-active')">Back</button>
        </footer>
    </div>
</div>