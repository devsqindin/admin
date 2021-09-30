//Code to edit
table.on('click', '#edit', function () {
    selid = table.row($(this).closest('tr')).data().id;
    $.ajax({
        url: '{{$link}}/oldform',
        datatype: 'json',
        data: {
            id: selid
            },
        success: function(data){
            console.log(data);
            $('#editItem').empty();
            $('#editItem').jsonForm(data);
            $('#editmod').addClass('is-active');
        }
    });
});
$('#EditSave').on('click',function(){
    var _packed = {};
    $('form#editItem :input').each(function(){
        _packed[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        url: '{{$link}}/changeItem',
        datatype: 'json',
        data: {
            _token: $("input[name='_token']").val(),
            id: selid,
            _packed
            },
        success: function(data){
            console.log(data);
            if(data){
                console.log('Yup');
            }else{
                console.log('Nop');
            }
            table.draw();
            $('#editmod').removeClass('is-active');
    }});
})