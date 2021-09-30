@push('css')
<link rel='stylesheet' href='/css/bulma-steps.min.css'/>
@endpush

<div class='card' style='padding:3%'>
    <br>
    <form id='_finalForm' method='POST' action="{{$link}}" enctype="multipart/form-data">
        @csrf

        @php
            $catr = $item->atr;
            $col = false;
            $_sec = $item->sections;  
        @endphp

        <div class='steps' id='stepper'>
            @foreach ($_sec as $sec_key => $sec_item)
                <div class='step-item'>
                    <div class='step-marker'>@php echo $sec_item['icon']@endphp</div>
                    <div class='step-details'>
                    <p class='step-title'>{{$sec_item['title']}}</p>
                    </div>
                </div>
            @endforeach
            <div class='steps-content'>
            @for($j=0;$j<count($_sec);$j++)
                <div class='step-content'>
                    @if(count($catr))
                        @foreach($catr as $name=>$info)
                            @php
                                $atr = $item->atr[$i];
                                $type = $info['type'];
                                $extra = $item->fillDetails($name)->form;
                            @endphp
                            @if($extra['OnCreate'] and $extra['stepper']==$j)
                                @if($col and $extra['newcolumn'])
                                    </div>
                                    @php
                                        $col = false;
                                    @endphp
                                @endif
                                @if(!$col and $extra['column'])
                                    <div class='columns'>
                                    @php
                                        $col = true;
                                    @endphp
                                @endif
                                @if($col and !$extra['column'])
                                    </div>
                                    @php
                                        $col = false;
                                    @endphp
                                @endif	
                                @if(!$extra['override'])
                                    @include('BuTTER.components.inputlocal.'.$type)
                                @else
                                    @include('BuTTER.components.inputlocal.'.$extra['replacement'])
                                @endif
                            @endif
                            
                        @endforeach
                        @if($col)
                            </div>
                            @php
                                $col = false
                            @endphp
                        @endif
                    @endif
                </div>
            @endfor
            </div>
            <div class="steps-actions">
                <div class="steps-action">
                    <a href="#" data-nav="previous" class="button is-light">Previous</a>
                </div>
                <div class="steps-action">
                    <div class='buttons has-addons'>
                        <a  data-nav="next" class="button is-link">Next</a>
                        <button disabled='true' data-nav="final" class='button is-success'>Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<br>
<a class='button' href="{{$link}}">Back</a>

@push('js')
<script src='/js/bulma-steps.min.js'>
</script>
<script>
    new bulmaSteps(document.getElementById('stepper'),{
        onShow: function(id){
            if(id=={{count($_sec)-1}}){
                $('button[data-nav="final"]').attr('disabled',false);
            }else{
                $('button[data-nav="final"]').attr('disabled',true);
            }
        }
    });
</script>
@endpush