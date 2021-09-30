<!-- GERAL -->
@foreach($arrfaq as $kp=>$faq)
  <button class="accordion blue">{{$faq['nome']}}</button>
  <div class="panel">

  @if($faq['perguntas'])
    @foreach($faq['perguntas'] as $perg)
      @if($perg->type==null)
      <button class="accordion acperg">{{$perg->pergunta}}</button>
      <div class="panel">
        <p>{!!$perg->resposta!!}</p>
      </div>
      @else
      <h3>{{$perg->pergunta}}</h3>
      @endif
    @endforeach
  @endif
  </div>
@endforeach
<!-- END GERAL -->