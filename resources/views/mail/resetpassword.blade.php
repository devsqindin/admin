@component('mail::message')

    Você solicitou uma redefinição de senha para a sua conta no Qindin. Caso esta solicitação não tenha sido feita por você ignore este e-mail.
    <div style="text-align:center;margin-top:20px">
        <a href="https://api.desbankei.com.br/reset-password?resettoken={{$resetToken}}" style="text-decoration:none;background-color:dodgerblue;color:white;padding:10px;font-size:20px;border:0px;cursor:pointer;border-radius:10px">
            Redefinir Senha
        </a>
    </div>

@endcomponent