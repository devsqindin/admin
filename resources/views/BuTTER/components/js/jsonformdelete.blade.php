table.on('click', '#delete', function (e) {
    e.preventDefault();
    
    selid = table.row($(this).closest('tr')).data().id;

    $('#delmod').addClass('is-active');

});
$('#delConfirm').on('click',function(){
    console.log(selid);
    $.ajax({
        url: '{{$link}}/destroyItem',
        datatype: 'json',
        data:{
            selid
        },
        success: function(data){
            console.log(data);
            table.draw();
            $('#delmod').removeClass('is-active');
        }
    });
});