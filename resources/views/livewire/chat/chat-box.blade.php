
<div 

x-data="{
    height:0,
    elementoConversa:document.getElementById('conversa'),
}"

{{-- x-init roda assim que a página é carregada --}}

x-init="
    height = elementoConversa.scrollHeight;
    mostrarNotificacao = false;
    $nextTick ( () => elementoConversa.scrollTop = height) //O $nextTick é uma propriedade mágica que permite executar uma expressão somente após o Alpine ter atualizado o DOM reativo, útil para interagir com o DOM após as atualizações de dados.
    
    Echo.private('conversa.{{ $conversaSelecionada->id }}')
        .listen('MensagemLida', (e) => {
            setTimeout(() => {
                $wire.call('atualizar');
            }, 1000); // Ocultar a notificação após 2 segundos
        });

    Echo.private('conversa.{{ $conversaSelecionada->id }}')
        .listen('MensagemLida', (e) => {
            console.log('Ler mensagens ao entrar na conversa');
            setTimeout(() => {
                $wire.call('atualizar');
            }, 1000); // Aguardar 1 segundo para ler as mensagens
        });
"

@scroll-bottom.window = "
     $nextTick(()=> elementoConversa.scrollTop= elementoConversa.scrollHeight);
"

class="w-full overflow-hidden">

    <div class="border-b flex flex-col overflow-y-scroll grow h-full">

    {{-- Header--}}
    
    <header class="w-full sticky inset-x-0 flex pb-[5px] pt-[5px] top-0 z-10 bg-white border-b " >

        <div class="flex w-full items-center px-2 lg:px-4 gap-2 md:gap-5">
            <a class="shrink-0 lg:hidden" href="#">
    
    
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                  </svg>
                  
            </a>
    
    
            {{-- avatar --}}
    
            <div class="shrink-0">
                <x-avatar class="h-9 w-9 lg:w-11 lg:h-11" />
            </div>
    
            <h6 class="font-bold truncate"> {{$conversaSelecionada->getDestinatario()->name}} </h6>  {{-- Conversa selecionada, que vem da blade main, retorna uma conversa, aí podemos chamar metodos contidos nesse model--}}
        </div>
    </header>

    {{-- Body--}} {{-- @class pode condicionalmente carregar atributos css baseado no valor de variaveis--}}
    
    <main 
     @scroll = "
        {{-- se der scroll até o topo do elemneto atual (main), chamar a função que carrega mais mensagens --}}

        scropTop = $el.scrollTop;

        if ($el.scrollTop <= 0) {
            $wire.carregarMaisMensagens();
        }
     "
    
     @atualiza-altura-chat.window="

     antigaAltura = $el.scrollHeight; // Guarda a altura antes de carregar mais conteúdo
 
     // Deixa Alpine.js carregar mais conteúdo primeiro

     //$nextTick é uma propriedade mágica que permite executar uma determinada expressão APÓS o Alpine ter atualizado reativamente o DOM. Isso é útil quando você precisa interagir com o estado do DOM depois que ele já refletiu as atualizações de dados que você fez.

     $nextTick(() => {
         novaAltura = $el.scrollHeight; // Nova altura após carregar conteúdo
         diferenca = novaAltura - antigaAltura; // Diferença causada pelo carregamento
 
         $el.scrollTop += diferenca; // Mantém o scroll na mesma posição relativa
 
         console.log('Nova altura: ' + novaAltura + ' | Antiga altura: ' + antigaAltura + ' | ScrollTop ajustado: ' + $el.scrollTop);
     });
 "
 
    id="conversa" class="flex flex-col gap-3 p-2.5 overflow-y-auto  flex-grow overscroll-contain overflow-x-hidden w-full my-auto">
    
        @if ($mensagensCarregadas) {{-- se existe mensagens --}}

        @php
            $mensagemAnterior= null;
        @endphp

        @foreach ($mensagensCarregadas as $key => $mensagem) 

        @if ($key > 0)

        @php
            $mensagemAnterior= $mensagensCarregadas->get($key - 1); //pra verificar quando pode ofuscar ou não
        @endphp

        @endif

        <div 
        wire:key="{{time().$key}}" {{-- Tem que rodar artisan queue:work para processar --}}
        @class([
            'max-w-[85%] md:max-w-[75%] flex w-auto gap-2 relative mt-2',
            'ml-auto' => $mensagem->remetente_id === Auth::id()
        ]) >
    
        <div @class([
            'shrink-0',
            'invisible' => $mensagemAnterior?->remetente_id == $mensagem->remetente_id,
            'hidden' => $mensagem->remetente_id === Auth::id()
        ])>
    
            <x-avatar />
    
        </div>
    
    
            {{-- Corpo da mensagem--}}

        <div @class(['flex flex-wrap text-[15px]  rounded-xl p-2.5 flex flex-col text-black bg-[#f6f6f8fb]',
            'rounded-bl-none border  border-gray-200/40 '=> ![$mensagem->remetente_id === Auth::id()],
            'rounded-br-none bg-blue-500/80 text-white'=> $mensagem->remetente_id === Auth::id()  {{-- Condicionalmente muda o valor do balão de texto dependendo de quem o enviou --}}
        ])>
    
        <p class="whitespace-normal truncate text-sm md:text-base tracking-wide lg:tracking-normal">
            {{$mensagem->corpo}}
        </p>
    
        <div class="ml-auto flex gap-2 pt-1">
    
            <p @class([
                'text-xs ',
                'text-gray-500'=> ![$mensagem->remetente_id === Auth::id()],
                'text-white'=> $mensagem->remetente_id === Auth::id(),
            ]) >
    
                {{$mensagem->created_at->format('g:i a')}}  {{-- formata a hora no formato AM/PM --}}
    
            </p>
    
        {{-- Checar o status da mensagem, apenas se ela foi enviada pelo usuário dessa session--}}

        @if ($mensagem->remetente_id === Auth::id())

        <div>
            
            @if ($mensagem->foiLido())

            <!-- Ícones de "tick" -->
            <span class="text-gray-200">
                <!-- Icone de 'dois tique' -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                    <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                    <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                </svg>
            </span>

            @else 
        
            <span class="text-gray-200">
                <!-- Icone de 'um tique' -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                </svg>
            </span>

            @endif
        </div>
        
        @endif

        </div>
    
        </div>
    
        </div>

        @endforeach

        @endif
    
    </main>
    
    
    
    {{-- Footer: mandar mensagem--}}

    <footer class="shrink-0 z-10 bg-white inset-x-0">

        <div class=" p-2 border-t">

            {{-- Form para enviar a mensagem--}}
            <form 
             x-data="{body:@entangle('body')}"         {{-- Entangle permite trocar/compartilhar caracteristicas entre alpine e livewire .... no livewire 3 tira o defer--}}
             @submit.prevent="console.log('Alpine funcionando'); $wire.enviarMensagem()"  {{-- Ao enviar o fomr, vai chamar esse metodo no backend desse componente livewire --}}
            method="POST" autocapitalize="off">
                @csrf
                <input type="hidden" autocomplete="false" style="display:none">

                <div class="grid grid-cols-12">
                     <input 
                            x-model="body"
                            type="text"
                            autocomplete="off"
                            autofocus
                            placeholder="Escreva aqui sua mensagem"
                            maxlength="1700"
                            class="col-span-10 bg-gray-100 border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg  focus:outline-none"
                     >

                     <button x-bind:disabled="body.length <= 0" class="col-span-2" type='submit'>Enviar</button> {{--É para não deixar enviar se a parte de mensagem (body) estiver vazia--}}

                </div>

            </form>

            @error('body')

            <p> {{$message}} </p>
                
            @enderror

        </div>

    </footer>

    </div>
    
</div>
