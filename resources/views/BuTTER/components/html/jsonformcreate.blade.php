@csrf
<div id='newmod' class='modal'>
    <div id='newmodbg' onClick="$('#newmod').removeClass('is-active')" class="modal-background">
    </div>
    <div class='modal-card'>
        <header class='modal-card-head'>
            <p class="modal-card-title">Add Item</p>
        </header>
        <section class="modal-card-body">
        <form id='newItem'></form>
        <script>
            $.ajax({
                url: '{{$link}}/newform',
                datatype: 'json',
                success: function(data){
                    $('#newItem').jsonForm(data);
            }});
        </script>
        </section>
        <footer class='modal-card-foot'>
            <button id='Add' class="button is-primary">Add</button>
            <script>

            </script>
            <button id='Add' class="button" onClick="$('#newmod').removeClass('is-active')">Cancel</button>
        </footer>
    </div>
    <button class="modal-close is-large" onClick="$('#newmod').removeClass('is-active')" aria-label="close"></button>
</div>
<button class='button' onCLick="$('#newmod').addClass('is-active')">Add new item</button>
<br>
<br>