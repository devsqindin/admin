@extends('layouts.admin')

@push('css')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/jquery.json-viewer.css')}}">
<style type="text/css">
  .bg-purple {
    background-color: purple;
  }
  .error {
    color: red;
  }
  /*.cadok {
    display: none;
  }*/
  .sploading {
    display: none;
  }
</style>
@endpush

@push('js')
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/crud.js')}}"></script>
<script src="{{asset('dist/js/jquery.json-viewer.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<script type="text/javascript">
$(function(){
  let url = location.href.replace(/\/$/, "");
  if (location.hash) {
    const hash = url.split("#");
    $('#adminTabs a[href="#'+hash[1]+'"]').tab("show");
    url = location.href.replace(/\/#/, "#");
    history.replaceState(null, null, url);
    setTimeout(() => {
      $(window).scrollTop(0);
    }, 400);
  } 
  $('a[data-toggle="tab"]').on("click", function() {
    let newUrl;
    const hash = $(this).attr("href");
    if(hash == "#home") {
      newUrl = url.split("#")[0];
    } else {
      newUrl = url.split("#")[0] + hash;
    }
    //newUrl += "/";
    history.replaceState(null, null, newUrl);
  });

  $.pegaStatus = function(status) {
    var st = '<span style="font-weight:bold;color:gray;">Cadastro Incompleto</span>';
    switch(status) {
      case 1 : st = '<span style="font-weight:bold;color:orange;">Pendente Documentação</span>'; break;
      case 2 : st = '<span style="font-weight:bold;color:purple;">Análise Documentação</span>'; break;
      case 3 : st = '<span style="font-weight:bold;color:green;">Cadastro Completo</span>'; break;
      case 4 : st = '<span style="font-weight:bold;color:red;">Cadastro Bloqueado</span>'; break;
      case 5 : st = '<span style="font-weight:bold;color:red;">Recusa de Crédito</span>'; break;
      case 6 : st = '<span style="font-weight:bold;color:red;">Bloqueio Falta Pgto</span>'; break;
    }
    return st
  }

  /*$.importaDocFiducia = function() {
    $('#btImportaDoc').attr('disabled')
    $.post('{{URL::to('/')}}importa/documentos',{
        id_cliente: {{$clienteId}},
      }, function (data) {
        if (data.success){
          alert('Documentos importados com sucesso!')
        } else {
          alert('ERRO: '+data.message)
        }
      })
  }*/

  $('#btnEnvioDoc').click(function(){
    $('#dvEnvio select').val('');
    $('#dvEnvio input[name="arquivo"]').val('');
    $('#dvEnvio').show()
  })

  function get_extension(filename) {
      return filename.split('.').pop().toLowerCase();
  }
  
  $('#frmEnvio').ajaxForm({
    beforeSubmit: function(arr,$form,options) {
      if(($('#arquivoup')[0].files[0].size > 8388608 && (get_extension($('#arquivoup').val()) == 'jpg' || get_extension($('#arquivoup').val()) == 'jpeg'))) { 
        alert("O arquivo deve ser menor ou igual a 8Mb");
        return false
      }
    },
    success: function(data) {
      if (data.success) {
        alert('Arquivo enviado com sucesso');
        table2.ajax.reload();
        $('#dvEnvio').hide()

      }
    }
  })

  $.getJSON('{{URL::to('/')}}/api/cliente/{{$clienteId}}', function(data){
    data.renda_comprovada = moeda.formatarx(data.renda_comprovada)
    data.credito_aprovado = moeda.formatarx(data.credito_aprovado)
    data.limite_utilizado = moeda.formatarx(data.limite_utilizado)
    $('#limite_utilizado_orig').val(data.limite_utilizado)
    data.limite_total = moeda.formatarx(data.limite_total)
    $('#limite_disponivel_orig').val(data.limite_disponivel)
    data.limite_disponivel = moeda.formatarx(data.limite_disponivel)
    $('.profile-username').text(data.nome_completo)
    $('#formPessoais').populate(data);
    if (data.habita_cidade) {
      $('#habita_cidade_txt').val(data.habita_cidade)
      setTimeout($.habitaCidadeLoad(),1000)
    } else {
      setTimeout($.habitaCidadeLoad(),1000)
    }
    if (data.aceito == null) {
      $('#inp-aceito').removeAttr('disabled')
    } else {
      $('label[for="inp-aceito"]').css('color','green')
    }
    if (!data.belvo_link) {
      $('#belvotab, #belvo').hide()
    }
    $('#formEndereco').populate(data);
    $('#formFinanceiro').populate(data);
    $('#formCredito').populate(data);
    $('#mostra_renda_comprovada').text('R$ '+data.renda_comprovada);
    $('#mostra_limite_utilizado').text('R$ '+data.limite_utilizado);
    $('#mostra_limite_total').text('R$ '+data.limite_total);
    $('#mostra_limite_disponivel').text('R$ '+data.limite_disponivel);
    if (data.status == 1) {
      $('#divAceiteCadastro').show();
    }
    if (data.status > 1) {
      if (data.status == 4) {
        $('.btt1').show();
      } else {
        $('.btt2').show();
      }
    }
    $('#situacao_cadastro').html($.pegaStatus(data.status))

    @if(Auth()->user()->temPermissao('cadastro','leitura'))
    $('#formPessoais input, #formPessoais select, #formEndereco input, #formEndereco select, #formFinanceiro input, #formFinanceiro select').attr('disabled','disabled')
    @endif

  })

  /*$.aceiteCadastro = function(option) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.post('{{URL::to('/')}}/admin/cliente/{{$clienteId}}/cadastro/inicio',{
      accept: option
    }, function (data) {
      location.href=''
    })
  }*/

  $.acaoGerencia = function(option) {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.post('{{URL::to('/')}}/admin/cliente/{{$clienteId}}/cadastro',{
        accept: option
      }, function (data) {
        location.href=''
      })
  }

  var table = $('#tbFaturas').DataTable({
      processing: true,
      serverSide: true,
      ajax: { url: '{{URL::to('/')}}/api/cliente/{{$clienteId}}/faturas' },
      "oLanguage": {
          "sUrl": "/pt_BR.txt"
      },
      columns: [
          {data:'created_at',name:'created_at',render:function(data,type,row){
            return moment(data,"YYYY-MM-DD HH:mm").format('DD/MM/YYYY HH:mm')
          }},
          {data:'vencimento',name:'vencimento',render:function(data,type,row){
            return moment(data,"YYYY-MM-DD").format('DD/MM/YYYY')
          }},
          {data:'valor_total',name:'valor_total',render:function(data,type,row){
            return 'R$ '+moeda.formatarx(parseFloat(data));
          }},
          {data:'antecipa',name:'antecipa',render:function(data,type,row){
            var st = '';
            if (data==1) {
              st = 'Sim'
            }
            return st
          }},
          {data:'status',name:'status',render:function(data,type,row){
            var st = 'Não Emitida';
            switch(data) {
              case 0 : st = 'Não Emitida'; break;
              case 1 : st = '<span style="color:orange;">Emitida</span>'; break;
              case 2 : st = '<span style="color:green;">Paga</span>'; break;
              case 3 : st = '<span style="color:red;">Cancelada</span>'; break;
            }
            return st
          }},
          { 
          data: null,
          orderable: false,
          width: '20%',
          className: "center",
          defaultContent: 
          `<button type="button" class="btn btn-sm btedit btn-primary">Gerenciar</button>`
          }
      ]
  });

  table.on('click', '.btedit', function () {
      selid = table.row($(this).closest('tr')).data().id;
      console.log(selid);
      $.get('/cliente/{{$clienteId}}/fatura/'+selid, function(data){
        $('#modalConteudo').html(data).modal('show')
      })
  });

  var table2 = $('#tbDocumentos').DataTable({
      processing: true,
      serverSide: true,
      ajax: { url: '{{URL::to('/')}}/api/cliente/{{$clienteId}}/documentos' },
      "oLanguage": {
          "sUrl": "/pt_BR.txt"
      },
      "lengthChange": false,
      "searching": false,
      columns: [
          {data:'documento',name:'documento'},
          {data:'arquivo',name:'arquivo'},
          {data:'formato',name:'formato'},
          {data:'aceite',name:'aceite',render:function(data,type,row){
            @if(!Auth()->user()->temPermissao('documentos','leitura'))
            if (data == 0) {
              return '<button type="button" class="btn btn-sm btn-success btaceite" data-option="1">Aceitar</button>&nbsp;<button type="button" class="btn btn-sm btn-danger btaceite" data-option="2">Recusar</button>';
            } else if (data == 1) {
              return '<span style="color:green;">Aceito</span>&nbsp;&nbsp; <a href="javascript:;" class="btaceite" data-option="2" style="color:red;font-size:14px;" title="Recusar"><i class="fas fa-times"></i></a>';
            } else if (data == 2) {
              return '<span style="color:red;">Recusado</span>';
            }
            @else
            if (data == 1) {
              return '<span style="color:green;">Aceito</span>';
            } else if (data == 2) {
              return '<span style="color:red;">Recusado</span>';
            }
            @endif
          }}
      ]
  });

  @if(!Auth()->user()->temPermissao('documentos','leitura'))
  table2.on('click', '.btaceite', function () {
      selid = table2.row($(this).closest('tr')).data().id;
      console.log(selid+' option '+$(this).attr('data-option'));
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.post('{{URL::to('/')}}/admin/cliente/{{$clienteId}}/aceite',{
        id_documento: selid,
        aceite: $(this).attr('data-option')
      }, function (data) {
        if (data.success) {
          if (data.btImportaDoc) {
            $('#btImportaDoc').show();
          }
          table2.ajax.reload();
        }
      })
  });
  @endif

  var Base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
      var output = "";
      var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
      var i = 0;

      input = Base64._utf8_encode(input);

      while (i < input.length) {

        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
          enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
          enc4 = 64;
        }

        output = output +
        this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
        this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

      }

      return output;
    },

    // public method for decoding
    decode : function (input) {
      var output = "";
      var chr1, chr2, chr3;
      var enc1, enc2, enc3, enc4;
      var i = 0;

      input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

      while (i < input.length) {

        enc1 = this._keyStr.indexOf(input.charAt(i++));
        enc2 = this._keyStr.indexOf(input.charAt(i++));
        enc3 = this._keyStr.indexOf(input.charAt(i++));
        enc4 = this._keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
          output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
          output = output + String.fromCharCode(chr3);
        }

      }

      output = Base64._utf8_decode(output);

      return output;

    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
      string = string.replace(/\r\n/g,"\n");
      var utftext = "";

      for (var n = 0; n < string.length; n++) {

        var c = string.charCodeAt(n);

        if (c < 128) {
          utftext += String.fromCharCode(c);
        }
        else if((c > 127) && (c < 2048)) {
          utftext += String.fromCharCode((c >> 6) | 192);
          utftext += String.fromCharCode((c & 63) | 128);
        }
        else {
          utftext += String.fromCharCode((c >> 12) | 224);
          utftext += String.fromCharCode(((c >> 6) & 63) | 128);
          utftext += String.fromCharCode((c & 63) | 128);
        }

      }

      return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
      var string = "";
      var i = 0;
      var c = c1 = c2 = 0;

      while ( i < utftext.length ) {

        c = utftext.charCodeAt(i);

        if (c < 128) {
          string += String.fromCharCode(c);
          i++;
        }
        else if((c > 191) && (c < 224)) {
          c2 = utftext.charCodeAt(i+1);
          string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
          i += 2;
        }
        else {
          c2 = utftext.charCodeAt(i+1);
          c3 = utftext.charCodeAt(i+2);
          string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
          i += 3;
        }

      }

      return string;
    }

  }

  $.consultaBelvo = function(tipo) {
    $('.sploading[data-id="'+tipo+'"]').show();
    tipos = ['','owners','balances','accounts','incomes','transactions'];
    $.get('{{URL::to('/')}}/admin/belvo/'+tipos[tipo]+'/{{$clienteId}}', function(data) {
      $.abreJsonBelvoTratamentoErros(data.data)
      if (data.cache) {
        $('#jsonOpcoes').html('<a href="{{URL::to('/')}}/admin/belvo/'+tipos[tipo]+'/{{$clienteId}}/cache"><i class="fa fa-times" style="color:red;"></i> Remover dados em cache</a>')
      }
      $('.sploading[data-id="'+tipo+'"]').hide();
    })
  }

  $.abreJson = function(data) {
    try {
      var input = eval('(' + Base64.decode(data) + ')');
    }
    catch (error) {
      return alert("Cannot eval JSON: " + error);
    }
    $('#json-renderer').html('#json-renderer').jsonViewer(input);
    $('#modalJson').modal('show')
  }

  $.abreJsonBelvoTratamentoErros = function(data) {

    var responseBelvoEndpoint = JSON.parse(Base64.decode(data));
    var input = responseBelvoEndpoint;
    console.log("DADOS RECEBIDOS DA API BELVO/CACHE: ");
    console.log(input);
    var solution = '';
    var requestId = '';
    
    try {
   
      if(input[0].message === 'Belvo is unable to process the request due to an internal system issue or to an unsupported response from an institution') {
        // https://developers.belvo.com/docs/belvo-errors -> 500 service_unavailable + unexpected_error
        requestId = input[0].request_id;
        input = 'O parceiro Belvo não pode processar a solicitação devido a um problema interno ou a uma resposta não suportada da instituição bancária';
        solution = 'Você pode tentar novamente mais tarde. Se você continuar recebendo esse erro, entre em contato conosco pelo e-mail support@belvo.com, certificando-se de incluir o request_id que você recebeu na mensagem.';
        input = "<strong>SITUAÇÃO:</strong>" + input + "\n" + "<strong>SOLUÇÃO:</strong> " + solution + "\n" + "<strong>Request ID:</strong> " + requestId;
        
        $('#belvo-erro-renderer').html(input);
        $('#modalConteudo').modal('show');
      } else if(input[0].code === 'request_timeout') {
        
        // https://docs.belvo.com/#operation/RetrieveAccounts -> 400
        requestId = input[0].request_id;
        input = 'A solicitação para a API Belvo expirou, você pode tentar pedir menos dados ou tentar mais tarde.';
        solution = 'A Api não está conseguindo lidar com o volume de dados solicitado, tente novamente mais tarde ou solicite os dados direto para o cliente.';
        input = "<strong>SITUAÇÃO:</strong>" + input + "\n" + "<strong>SOLUÇÃO:</strong> " + solution + "\n" + "<strong>Request ID:</strong> " + requestId;
        
        $('#belvo-erro-renderer').html(input);
        $('#modalConteudo').modal('show');
      } else if(input[0].code === 'too_many_sessions') {
        
        // https://docs.belvo.com/#operation/RetrieveAccounts -> 400
        requestId = input[0].request_id;
        input = 'Não está sendo possível fazer o login na conta do cliente para obter dados bancários, pois, já foi aberta uma sessão com a instituição para essa conta.';
        solution = 'Solicite ao cliente para que encerre sessões ativas da sua conta bancária ou tente novamente mais tarde.';
        input = "<strong>SITUAÇÃO:</strong>" + input + "\n" + "<strong>SOLUÇÃO:</strong> " + solution + "\n" + "<strong>Request ID:</strong> " + requestId;
        
        $('#belvo-erro-renderer').html(input);
        $('#modalConteudo').modal('show');
      } else if(input[0].code === 'login_error') {
        
        // https://docs.belvo.com/#operation/RetrieveAccounts -> 400
        requestId = input[0].request_id;
        input = 'As credenciais fornecidas para fazer login na instituição bancária do cliente estão inválidas.';
        solution = 'Entre em contato com o cliente para ter as informações para acesso a sua conta bancária atualizadas.';
        input = "<strong>SITUAÇÃO:</strong>" + input + "\n" + "<strong>SOLUÇÃO:</strong> " + solution + "\n" + "<strong>Request ID:</strong> " + requestId;
        
        $('#belvo-erro-renderer').html(input);
        $('#modalConteudo').modal('show');
      } else if(input[0].code === 'institution_down' || input[0].code === 'institution_unavailable' || input[0].code === 'institution_inactive') {
        
        // https://docs.belvo.com/#operation/RetrieveAccounts -> 400
        requestId = input[0].request_id;
        input = 'Problemas na conexão com o banco do cliente.';
        solution = 'Tente novamente mais tarde, ou se o problema persistir, entre em contato com o suporte Belvo pelo email support@belvo.com, certificando-se de incluir o request_id que você recebeu na mensagem.';
        input = "<strong>SITUAÇÃO:</strong>" + input + "\n" + "<strong>SOLUÇÃO:</strong> " + solution + "\n" + "<strong>Request ID:</strong> " + requestId;
        
        $('#belvo-erro-renderer').html(input);
        $('#modalConteudo').modal('show');
      } else if(input[0].code === 'token_required') {
        
        // https://docs.belvo.com/#operation/RetrieveAccounts -> 400
        requestId = input[0].request_id;
        input = 'Para a conexão bancária desse cliente é necessário um token de autentição.';
        solution = 'Entre em contato com o cliente para ter as informações bancárias necessárias.';
        input = "<strong>SITUAÇÃO:</strong>" + input + "\n" + "<strong>SOLUÇÃO:</strong> " + solution + "\n" + "<strong>Request ID:</strong> " + requestId;
        
        $('#belvo-erro-renderer').html(input);
        $('#modalConteudo').modal('show');
      }
      else {
        
        $('#json-renderer').html('#json-renderer').jsonViewer(input);
        $('#modalJson').modal('show')
      }
    }
    catch (error) {
      
      return alert("Cannot eval JSON: " + error);
    }    
  }

  /*
      de 0 ate transactionsJson.length
      0 ->
        interessante: value_date, amount, type

        switch value_date
          case 01
            if type OUTFLOW
              varJanOutflow = varJanOutflow + amount

            if type INFLOW
              varJanInflow = varJanInflow + amount
          case 02
            if type OUTFLOW
              varFevOutflow = varFevOutflow + amount

            if type INFLOW
              varFevInflow = varFevInflow + amount
  */
  $.automatizaBelvo = function() {

    alert("Consultando Belvo no período mínimo de: 12 Meses!");

    $.get('{{URL::to('/')}}/admin/belvo/transactions/{{$clienteId}}', function(data) {

      console.log("GET feito: /admin/belvo/transactions/{{$clienteId}}");

      var transactionsBase64 = Base64.decode(data.data);
      var transactionsJson = jQuery.parseJSON(transactionsBase64);
      console.log("OBJETO JSON INTEIRO RECUPERADO: ");
      console.log(transactionsJson);

      $.calculaBelvoTransactionsInflowOutflow(transactionsJson);
    })
  }

  $.calculaBelvoTransactionsInflowOutflow = function(transactionsJson) {

    var inflowSoma = 0;
    var outflowSoma = 0;
    let inflowOutflowCalcByMonth = {}; 
    
    var tabelaBelvoAutStyle = "<style> table, th, td { border:1px solid black; text-align:center;}</style>";
    var tabelaBelvoAutTitulo = "<table><tr><th>Rótulos de Linha</th><th>Soma de Creditos</th><th>Soma de Débitos</th></tr>";
    var tabelaBelvoAutLinha = "";
    
    if(transactionsJson.length > 0) {

      console.log("Objeto JSON com contéudo para ser processado");
      for(var x = transactionsJson.length -1; x >= 0; x--) {

        let valueDate = "01/" + transactionsJson[x].value_date.substring(5,7) + "/" + transactionsJson[x].value_date.substring(0,4) // e.g.: 01/06/2020 (01/mm/aaaa)

        if(x === transactionsJson.length -1) {

          let contador = 0;
          let calcDozeMesesArray = valueDate;
          let mesSubsequente = parseInt(transactionsJson[x].value_date.substring(5,7), 10);
          let anoSubSequente = parseInt(transactionsJson[x].value_date.substring(0,4), 10);

          for (var w = 1; w < 12; w++) {

            inflowOutflowCalcByMonth[calcDozeMesesArray] = [0, 0];
            mesSubsequente = mesSubsequente + 1;

            if(mesSubsequente > 12) {

              mesSubsequente = 1;
              anoSubSequente = anoSubSequente + 1;
            }

            if (mesSubsequente.toString().length < 2) {

              calcDozeMesesArray = "01/" + "0" + mesSubsequente.toString() + "/" + anoSubSequente;
            } else {

              calcDozeMesesArray = "01/" + mesSubsequente.toString() + "/" + anoSubSequente;
            }   
          }

          console.log("inflowOutflowCalcByMonth CALCULADO 12 MESES: ");
          console.log(inflowOutflowCalcByMonth);
        }

        if(transactionsJson[x].type === "OUTFLOW") {

          if(inflowOutflowCalcByMonth.hasOwnProperty(valueDate)) {

            /* REGRA OUTFLOW DE CONSIDERAR SÓ LANÇAMENTOS PARA CONTA CORRENTE

              PARA CLIENTE NÚMERO 1 - 2368 -> Deu certinho, pouca diferença com oq tinha na planilha
              PARA CLIENTE NÚMERO 2 - 124 -> Deu grande diferença de valores entre planilha e programa, ao tirar essa regra os valores ficam identicos entre as fontes.
              PARA CLIENTE NÚMERO 3 - 639 -> Deu certinho
              PARA CLIENTE NÚMERO 4 - 1066 -> Deu grande diferença, pq o belvo dele a ordem dos meses veio bagunçado (primeiro mes é 7/2020) e no extrato veio 10/2020.

              Para resolver isso, implementar a seguinte melhoria ao construir o objeto inflowOutflowCalcByMonth:
              myObjs = [{"date": "01/01/2020", "inflow": 1, "ouflow": 2}, {"date": "01/02/2021", "inflow": 1, "ouflow": 2}, {"date": "01/03/2019", "inflow": 1, "ouflow": 2}, {"date": "01/05/2019", "inflow": 1, "ouflow": 2}, {"date": "01/06/2020", "inflow": 1, "ouflow": 2}]

              Dessa maneira podemos ordenar por data:

              myObjs.sort(function(a, b) {
                var keyA = new Date(a.date),
                  keyB = new Date(b.date);
                // Compare the 2 dates
                if (keyA < keyB) return -1;
                if (keyA > keyB) return 1;
                return 0;
              });

              console.log(myObjs);
            */

            //if(transactionsJson[x].account.type == "SALDO DA CONTA-CORRENTE: 1") {

                inflowOutflowCalcByMonth[valueDate][1] = inflowOutflowCalcByMonth[valueDate][1] + transactionsJson[x].amount;
            //}
          } else {

            inflowOutflowCalcByMonth[valueDate] = [0, transactionsJson[x].amount]
          }

        } else if(transactionsJson[x].type === "INFLOW") {

          if(inflowOutflowCalcByMonth.hasOwnProperty(valueDate)) {

            // Somando Créditos e Debitos apenas do tipo "Lançamentos de Conta Corrente"
            if(transactionsJson[x].account.category != "CREDIT_CARD") {

              inflowOutflowCalcByMonth[valueDate][0] = inflowOutflowCalcByMonth[valueDate][0] + transactionsJson[x].amount;
            }
          } else {

            inflowOutflowCalcByMonth[valueDate] = [transactionsJson[x].amount, 0]
          } 
        }
      } 

      for (var prop in inflowOutflowCalcByMonth) {

        inflowOutflowCalcByMonth[prop][0] = $.replaceNumberWithCommas(inflowOutflowCalcByMonth[prop][0]);
        inflowOutflowCalcByMonth[prop][1] = $.replaceNumberWithCommas(inflowOutflowCalcByMonth[prop][1]);
      }
      
      for (var prop in inflowOutflowCalcByMonth) {
      // ctrl+shift+k (para abrir o console no mozilla firefox)

        tabelaBelvoAutLinha += "<tr><td>" + prop + "</td><td>" + inflowOutflowCalcByMonth[prop][0] + "</td><td>" + inflowOutflowCalcByMonth[prop][1] + "</td></tr>";
      }
      tabelaBelvoAutLinha += "</table>";

      //var tabelaBelvoAutLinha = "<tr><td>TOTAL</td><td>" + inflowSoma + "</td><td>" + outflowSoma + "</td></tr></table>";
      var tabelaBelvoAut = tabelaBelvoAutStyle + tabelaBelvoAutTitulo + tabelaBelvoAutLinha;

      $.abreJsonBelvoAutomatizado(tabelaBelvoAut);

      // html renderizado funcionando, agora implementar os passos da logica descrita na linha 884
      // preciso dar o round jquery tbm pra valores monetarios R$

      /**
       obj[jan] = 10
      obj[fev] = 20
      */
    } else {
        console.log("Objeto JSON sem conteúdo para ser processado");
      
        let msgSemDadosProcessar = "<strong>Não foram encontrados dados bancários para serem processados.</strong>";
        tabelaBelvoAutLinha += "<tr>" + msgSemDadosProcessar +"</tr>";
        tabelaBelvoAutLinha += "</table>";
        
        var tabelaBelvoAut = tabelaBelvoAutStyle + tabelaBelvoAutLinha;

        $.abreJsonBelvoAutomatizado(tabelaBelvoAut);
    }
  }

  $.abreJsonBelvoAutomatizado = function(tabelaBelvoAut) {

    var input = tabelaBelvoAut;
    $('#belvo-automatizado-renderer').html(input);
    $('#modalBelvoAutomatizado').modal('show');
  }

  $.replaceNumberWithCommas = function(yourNumber) {
    //Seperates the components of the number
    var n= yourNumber.toString().split(".");
    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    //Combines the two sections
    return n.join(",");

    /*
      let n = 234234.555;
      let str = n.toLocaleString("pt-BR");
      console.log(str); // "234,234.555"
      234.234,555
    */
  }

  var table3 = $('#tbHistorico').DataTable({
      processing: true,
      serverSide: true,
      ajax: { url: '{{URL::to('/')}}/api/cliente/{{$clienteId}}/historico' },
      "oLanguage": {
          "sUrl": "/pt_BR.txt"
      },
      "lengthChange": false,
      "searching": false,
      columns: [
          {data:'datahora',name:'datahora',render:function(data,type,row){
            return moment(data,"YYYY-MM-DD HH:mm:ss").format('DD/MM/YYYY HH:mm')
          }},
          {data:'operador',name:'operador'},
          {data:'acao',name:'acao'},
          {data:'descricao',name:'descricao',render:function(data,type,row){
            if(row.id_acao_historico == 10||row.id_acao_historico == 11||row.id_acao_historico == 12) {
              return '<a href="javascript:;" onclick="$.abreJson(\''+data+'\')">requisição</a>';
            } else {
              return data;
            }
          }},
          {data:'valor',name:'valor',render:function(data,type,row){
            if(row.id_acao_historico == 1) {
              return 'R$ '+moeda.formatar(parseFloat(data));
            } else if(row.id_acao_historico == 10||row.id_acao_historico == 11||row.id_acao_historico == 12) {
              return '<a href="javascript:;" onclick="$.abreJson(\''+data+'\')">resposta</a>';
            } else {
              return data;
            }
          }},
      ]
  });

  $.adminTabs = function(name) {
    $('#adminTabs a[href="#'+name+'"]').tab('show')
  }

  $.historicoCredito = function() {
    $.get('/cliente/{{$clienteId}}/creditos', function(data){
      $('#modalConteudo').html(data).modal('show')
    })
  }

  $('#formPessoais').validate()
  $('#formPessoais').ajaxForm({
    beforeSubmit: function(arr,$form,options) {
      if (!$('#formPessoais').valid()) {
        return false; 
      }
    },
    success: function(data) {
      if (data.success) {
        alert('Dados pessoais atualizados');
      }
    }
  });

  $('#formEndereco').validate()
  $('#formEndereco').ajaxForm({
    beforeSubmit: function(arr,$form,options) {
      if (!$('#formEndereco').valid()) {
        return false; 
      }
    },
    success: function(data) {
      if (data.success) {
        alert('Dados de endereço atualizados');
      }
    }
  });

  $('#formFinanceiro').validate()
  $('#formFinanceiro').ajaxForm({
    beforeSubmit: function(arr,$form,options) {
      if (!$('#formFinanceiro').valid()) {
        return false; 
      }
    },
    success: function(data) {
      if (data.success) {
        alert('Dados financeiros atualizados');
      }
    }
  });

  $('#formCredito').validate()
  $('#formCredito').ajaxForm({
    beforeSubmit: function(arr,$form,options) {
      if (!$('#formCredito').valid()) {
        return false; 
      }
    },
    success: function(data) {
      if (data.success) {
        alert('Informações de crédito atualizadas');
        location.reload()
      }
    }
  });

  $('#frmAcao').ajaxForm({
    // beforeSubmit: function(arr,$form,options) {
    //   if (!$('#formPessoais').valid()) {
    //     return false; 
    //   }
    // },
    success: function(data) {
      if (data.success) {
        alert('Ações atualizadas');
        location.reload()
      }
    }
  });

})
</script>
@endpush

@section('content')
<div class="container-fluid">
<div class="row">
  <div class="col-md-3">

    <!-- Profile Image -->
    <div class="card card-primary card-outline">
      <div class="card-body box-profile">
        {{-- <div class="text-center">
          <img class="profile-user-img img-fluid img-circle"
               src="../../dist/img/user4-128x128.jpg"
               alt="User profile picture">
        </div> --}}

        <h3 class="profile-username text-center"></h3>

        {{-- <p class="text-muted text-center">Software Engineer</p> --}}

        <ul class="list-group list-group-unbordered mb-3">
          <li class="list-group-item">
            <b>Renda Comp.</b> <span id="mostra_renda_comprovada" class="float-right">0</a>
          </li>
          <li class="list-group-item">
            <b>Limite Total</b> <span id="mostra_limite_total" class="float-right">0</a>
          </li>
          <li class="list-group-item">
            <b>Limite Utilizado</b> <span id="mostra_limite_utilizado" class="float-right">0</a>
          </li>
          <li class="list-group-item">
            <b>Limite Disponível</b> <span id="mostra_limite_disponivel" class="float-right">0</a>
          </li>
          <li class="list-group-item">
            <b>Situação</b> <span id="situacao_cadastro" class="float-right">--</a>
          </li>
        </ul>

        {{-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <!-- About Me Box -->
    {{-- <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">About Me</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <strong><i class="fas fa-book mr-1"></i> Education</strong>

        <p class="text-muted">
          B.S. in Computer Science from the University of Tennessee at Knoxville
        </p>

        <hr>

        <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

        <p class="text-muted">Malibu, California</p>

        <hr>

        <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

        <p class="text-muted">
          <span class="tag tag-danger">UI Design</span>
          <span class="tag tag-success">Coding</span>
          <span class="tag tag-info">Javascript</span>
          <span class="tag tag-warning">PHP</span>
          <span class="tag tag-primary">Node.js</span>
        </p>

        <hr>

        <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
      </div>
      <!-- /.card-body -->
    </div> --}}
    <!-- /.card -->
  </div>
  <!-- /.col -->
  <div class="col-md-9">
    <div class="card">
      <div class="card-header p-2" id="adminTabs">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">Timeline</a></li>
          @if(Auth()->user()->temPermissao('cadastro','acesso'))
          <li class="nav-item"><a class="nav-link" href="#cadastro" data-toggle="tab">Cadastro</a></li>
          @endif
          @if(Auth()->user()->temPermissao('documentos','acesso'))
          <li class="nav-item"><a class="nav-link" href="#documentos" data-toggle="tab">Documentos</a></li>
          @endif
          @if(Auth()->user()->temPermissao('creditos','acesso'))
          <li class="nav-item" id="belvotab"><a class="nav-link" href="#belvo" data-toggle="tab">Bancário (Belvo)</a></li>
          <li class="nav-item"><a class="nav-link" href="#creditos" data-toggle="tab">Créditos</a></li>
          @endif
          @if(Auth()->user()->temPermissao('faturas','acesso'))
          <li class="nav-item"><a class="nav-link" href="#faturas" data-toggle="tab">Faturas</a></li>
          @endif
          <li class="nav-item"><a class="nav-link" href="#historico" data-toggle="tab">Histórico</a></li>
          @if(Auth()->user()->temPermissao('acoes','acesso') && !Auth()->user()->temPermissao('acoes','leitura'))
          <li class="nav-item"><a class="nav-link" href="#acoes" data-toggle="tab">Ações</a></li>
          @endif
        </ul>
      </div><!-- /.card-header -->
      <div class="card-body">
        <div class="tab-content">
          <div class="active tab-pane" id="timeline">
            <!-- The timeline -->
            <div class="timeline timeline-inverse" style="height:60vh;overflow-y:scroll;">
              @foreach($timeline as $tline)
              <!-- timeline time label -->
              <div class="time-label">
                <span class="{{$tline['bg']}}">
                  {{date("d M Y",strtotime($tline['datetime']))}}
                </span>
              </div>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <div>
                <i class="{{$tline['icon']}} {{$tline['bg']}}"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> {{date("H:i",strtotime($tline['datetime']))}}</span>

                  <h3 class="timeline-header"><a href="javascript:void(0);" onclick="$.adminTabs('{{$tline['link']}}')">{{$tline['action']}}</a></h3>

                  {{-- <div class="timeline-body">
                    Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                    weebly ning heekya handango imeem plugg dopplr jibjab, movity
                    jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                    quora plaxo ideeli hulu weebly balihoo...
                  </div> --}}
                  <div class="timeline-footer">
                    {{-- <a href="#" class="btn btn-primary btn-sm">Aceitar</a>
                    <a href="#" class="btn btn-danger btn-sm">Recusar</a> --}}
                  </div>
                </div>
              </div>
              @endforeach
              <!-- END timeline item -->
              {{-- <!-- timeline item -->
              <div>
                <i class="fas fa-user bg-info"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> 5 mins ago</span>

                  <h3 class="timeline-header border-0"><a href="#">Sarah Young</a> accepted your friend request
                  </h3>
                </div>
              </div>
              <!-- END timeline item -->
              <!-- timeline item -->
              <div>
                <i class="fas fa-comments bg-warning"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> 27 mins ago</span>

                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                  <div class="timeline-body">
                    Take me to your leader!
                    Switzerland is small and neutral!
                    We are more like Germany, ambitious and misunderstood!
                  </div>
                  <div class="timeline-footer">
                    <a href="#" class="btn btn-warning btn-flat btn-sm">View comment</a>
                  </div>
                </div>
              </div>
              <!-- END timeline item -->
              <!-- timeline time label -->
              <div class="time-label">
                <span class="bg-success">
                  3 Jan. 2014
                </span>
              </div>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <div>
                <i class="fas fa-camera bg-purple"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> 2 days ago</span>

                  <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                  <div class="timeline-body">
                    <img src="http://placehold.it/150x100" alt="...">
                    <img src="http://placehold.it/150x100" alt="...">
                    <img src="http://placehold.it/150x100" alt="...">
                    <img src="http://placehold.it/150x100" alt="...">
                  </div>
                </div>
              </div>
              <!-- END timeline item -->
              --}}
              <div>
                <i class="far fa-clock bg-gray"></i>
              </div>
            </div>
          </div>
          <!-- /.tab-pane -->
          @if(Auth()->user()->temPermissao('cadastro','acesso'))
          <div class="tab-pane" id="cadastro">
              {{-- <div id="divAceiteCadastro" class="jumbotron" style="padding:2rem;display:none;">
                <h4>Este cadastro ainda não foi autorizado para uso da Qindin</h4>
                <button type="button" class="btn btn-success" onclick="$.aceiteCadastro('S')">Autorizar</button> <button type="button" class="btn btn-danger" onclick="$.aceiteCadastro('N')">Recusar</button>
              </div> --}}
              <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                  <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#dados-pessoais" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Dados Pessoais</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#dados-endereco" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Endereço</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#dados-financeiro" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Financeiro/Bancário</a>
                    </li>
                  </ul>
                </div>
                <div class="card-body">
                  <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade show active" id="dados-pessoais" role="tabpanel" aria-labelledby="dados-pessoais-tab">
                       @include('forms.pessoais')
                    </div>
                    <div class="tab-pane fade" id="dados-endereco" role="tabpanel" aria-labelledby="dados-endereco-tab">
                      @include('forms.endereco')
                    </div>
                    <div class="tab-pane fade" id="dados-financeiro" role="tabpanel" aria-labelledby="dados-financeiro-tab">
                       @include('forms.financeiro')
                    </div>
                  </div>
                </div>
                <!-- /.card -->
              </div>
          </div>
          <!-- /.tab-pane -->
          @endif
          @if(Auth()->user()->temPermissao('documentos','acesso'))
          <div class="tab-pane" id="documentos">
            <h3><b>Documentos do Cliente</b></h3>
            <p>Arquivos enviados</p>
            <button id="btnEnvioDoc" type="button" class="btn btn-primary">Enviar Documentos</button>
            <div id="dvEnvio" style="display:none;">
              <form id="frmEnvio" action="{{URL::to('/')}}/admin/enviodoc" method="post" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-4">
                    <select name="tipo" class="form-control">
                      <option value="" class="form-control">Selecione...</option>
                      @foreach($tipoDocumento as $tdoc)
                      <option value="{{$tdoc->id}}">{{$tdoc->descricao}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <input type="file" name="arquivo" id="arquivoup" accept=".jpg,.jpeg,.pdf" class="form-control">
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-success">Enviar</button>
                  </div>
                </div>
                <input type="hidden" name="cliente_id" value="{{$clienteId}}">
                @csrf
              </form>
            </div>
            <table id="tbDocumentos" class="table table-striped" style="width:100%;">
              <thead>
                <tr>
                  <th>Documento</th>
                  <th>Arquivo</th>
                  <th>Formato</th>
                  <th>Aceite</th>
                </tr>
              </thead>
            </table>
            {{-- <br>
            <button type="button" id="btImportaDoc" onclick="$.importaDocFiducia()" class="btn btn-primary btn-success" @if(!$btImportaDoc)style="display:none;"@endif>Enviar Documentos a Fidúcia</button> --}}
          </div>
          @endif
          @if(Auth()->user()->temPermissao('creditos','acesso'))
          <div class="tab-pane" id="belvo">
            <h3><b>Informações Bancárias (Belvo)</b></h3>
            <p>Selecione qual informação deseja consultar</p>
            <hr>
            <ul>
              <li><a href="#" onclick="$.consultaBelvo(1);">Dados do Proprietário/Conta</a> <i class="fas fa-spinner fa-pulse sploading" data-id="1"></i></li>
              <li><a href="#" onclick="$.consultaBelvo(2);">Saldos</a> <i class="fas fa-spinner fa-pulse sploading" data-id="2"></i></li>
              <li><a href="#" onclick="$.consultaBelvo(3);">Tipo(s) de Conta</a> <i class="fas fa-spinner fa-pulse sploading" data-id="3"></i></li>
              <li><a href="#" onclick="$.consultaBelvo(4);">Renda Verificada</a> <i class="fas fa-spinner fa-pulse sploading" data-id="4"></i></li>
              <li><a href="#" onclick="$.consultaBelvo(5);">Extratos</a> <i class="fas fa-spinner fa-pulse sploading" data-id="5"></i></li>
              <li><a href="#" onclick="$.automatizaBelvo();">Belvo Automatização</a> <i class="fas fa-spinner fa-pulse sploading" data-id="5"></i></li>
            </ul>
          </div>
          <div class="tab-pane" id="creditos">
            <h3><b>Situação Atual de Crédito</b></h3>
            @include('forms.credito')
          </div>
          @endif
          @if(Auth()->user()->temPermissao('faturas','acesso'))
          <div class="tab-pane" id="faturas">
            <h3><b>Faturas</b></h3>
            <table id="tbFaturas" class="table table-striped" style="width:100%;">
              <thead>
                <tr>
                  <th>Data/Hora Emissão</th>
                  <th>Data Vencimento</th>
                  <th>Valor Total</th>
                  <th>Adianta</th>
                  <th>Situação</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
          @endif
          <div class="tab-pane" id="historico">
            <h3><b>Histórico de Atividades</b></h3>
            <table id="tbHistorico" class="table table-striped" style="width:100%;">
              <thead>
                <tr>
                  <th>Data/Hora</th>
                  <th>Operador/Tomador</th>
                  <th>Ação</th>
                  <th>Descrição</th>
                  <th>Valor</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
          @if(Auth()->user()->temPermissao('acoes','acesso') && !Auth()->user()->temPermissao('acoes','leitura'))
          <div class="tab-pane" id="acoes">
            <h3><b>Ações de Gerenciamento</b></h3>
            <form id="frmAcao" method="post" action="{{URL::to('/')}}/admin/cliente/{{$clienteId}}/acao">
              <p><label>Tipo de Ação:</label>
              <select name="acao_executar" class="form-control">
                <option value="">Selecione...</option>
                @foreach($acoes as $akey=>$aname) {
                <option value="{{$akey}}">{{$aname}}</option>
                @endforeach
              </select>
              <label>Motivo:</label>
              <select name="acao_motivo" class="form-control">
                <option value="">Selecione...</option>
                @foreach($motivos as $motivo)
                <option value="{{$motivo->id}}">{{$motivo->nome}}</option>
                @endforeach
              </select>
              </p>
              <p><button type="submit" class="btn btn-primary">Executar Ação</button></p>
              @csrf
            </form>
          </div>
          @endif
        </div>
        <!-- /.tab-content -->
      </div><!-- /.card-body -->
    </div>
    <!-- /.nav-tabs-custom -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
</div><!-- /.container-fluid -->

<div class="modal fade" id="modalConteudo">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Falha na Solicitação</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div id="belvoErroOpcoes"></div>
        <!--<div style="overflow-y:scroll;max-height:100vh;">-->
        <pre id="belvo-erro-renderer"></pre>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="modalBelvoAutomatizado">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Resultado Belvo Automatizado (transactions)</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div id="belvoAutomatizadoConteudo"></div>
        <!--<div style="overflow-y:scroll;max-height:100vh;">-->
        <pre id="belvo-automatizado-renderer"></pre>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="modalJson">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">JSON</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div id="jsonOpcoes"></div>
        <div style="overflow-y:scroll;max-height:80vh;">
        <pre id="json-renderer"></pre>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
      </div>

    </div>
  </div>
</div>
@endsection