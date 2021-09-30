@extends('layouts.agendamento')

@section('content')
<div class='container bg-white border border-dark p-5' style='border-radius:20px'>
    @if($alteraemail)
    <h2 class='text-center'>Seu e-mail foi alterado com sucesso</h2>
    @else
    <h2 class='text-center'>Este link de alterar o e-mail é invalido ou está expirado.</h2>
    <p class="text-center">Solicite novamente a alteração de e-mail e confirme em um novo e-mail</p>
    @endif

</div>
@endsection