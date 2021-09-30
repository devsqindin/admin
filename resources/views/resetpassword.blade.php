@extends('layouts.agendamento')

@section('content')
<div class='container bg-white p-5 dvcenter'>
    <h3 class='mb-3'>Redefinir senha</h3>
    <form id="resetForm" method="POST" action="/reset-password">
        @csrf
        <input hidden value="{{$resetToken}}" name="_userToken"/>
        <div class="form-group">
            {{-- <label for="passwordInput">Nova Senha</label> --}}
            <input type="password" class="form-control" id="passwordInput" name="newPass" type="hidden" placeholder="Nova Senha">
            <small id="tooSmall" class="form-text text-danger" hidden>A senha deve ter no mínimo 6 caracteres.</small>
        </div>
        <div class="form-group">
            {{-- <label for="confirmPasswordInput">Confirme Nova Senha</label> --}}
            <input type="password" class="form-control" id="confirmPasswordInput" name="confirmPass" placeholder="Confirme Nova Senha">
            <small id="notRight" class="form-text text-danger" hidden>A senha e a confirmação não são iguais.</small>
        </div>
        <div class='clearfix'>
            <p class="password" style="color:#777;line-height:14px;font-size:14px;">Sua senha deve conter no mínimo 6 caracteres, sendo 1 letra maiúscula e 1 número.</p>
        </div>
        <div>
            <button type="button" id="Submit" class="btn btn-primary bt1" style="width:100%;">Alterar Senha</button>
        </div>
    </form>
</div>
@endsection 

@push('js')
    <script>
        $('#Submit').click(function(e){
            var password = $('#passwordInput').val();
            var rep_password = $('#confirmPasswordInput').val();
            var StrongPass = /^(?=\S*?[A-Z])(?=\S*?[0-9])\S{6,}$/;
            
            if (password != rep_password) {
              alert('Senhas diferentes');
              $('#passwordInput').focus();
              return false;
            } else if (password.length < 6) {
              alert('Senha precisa ter no mínimo 6 caracteres');
              $('#passwordInput').focus();
              return false;
            } else if (!StrongPass.test(password)) {
              alert('Sua senha deve conter no mínimo 6 caracteres, sendo 1 letra maiúscula e 1 número.');
              $('#passwordInput').focus();
              return false;
            }else{
                $('#resetForm').submit();
            }
        })
        $('#passwordInput').change(function(){
            $('#passwordInput').removeClass('is-invalid')
            $('#confirmPasswordInput').removeClass('is-invalid')
            $('#notRight').hide()
            $('#tooSmall').hide()
        })
        $('#confirmPasswordInput').change(function(){
            $('#passwordInput').removeClass('is-invalid')
            $('#confirmPasswordInput').removeClass('is-invalid')
            $('#notRight').hide()
            $('#tooSmall').hide()
        })
    </script>
@endpush