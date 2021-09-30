$('#Add').on('click',function(){
    var _pack = {};
    $('form#newItem :input').each(function(){
        _pack[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        url: '{{$link}}/newitem',
        datatype: 'json',
        data: {
            _token: $("input[name='_token']").val(),
            _method: 'POST',
            _pack
            },
        success: function(data){
            if(data){
                console.log('Yup');
            }else{
                console.log('Nop');
            }
            table.draw();
            $('#newmod').removeClass('is-active');
            $('#newItem').trigger('reset');
    }});
})