<div 
x-data="{type:'all'}" 

x-init="setTimeout(() => { 
            let conversaId = @json($conversaSelecionada ? $conversaSelecionada->id : null); //O json no Blade do Laravel é um helper que converte dados PHP em JSON para serem utilizados diretamente no JavaScript dentro da view. Ele é útil quando você precisa passar dados do backend para o frontend de forma segura e compatível com JavaScript. 

            let conversaAtual = document.getElementById('conversa-' + conversaId); 
            if (conversaAtual) {
                conversaAtual.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            //ouve evento de mesmo nome do backend; tem que ter esse nome de scroll-bottom mas não sei o motivo 
            Livewire.on('scroll-bottom', () => {

                setTimeout(() => {
                    let chatList = document.getElementById('chat-list');
                    if (chatList) {
                        chatList.scrollTo({ top: 0, behavior: 'smooth' });
                }, 900); 
            });

}, 150);" 
{{-- Dar um tempo antes dar scroll pra conversa selecionada, para carregar o resto da página (Alpine)--}}

class="flex flex-col transition-all h-full overflow-hidden">


    <header class="px-3 z-10 bg-white sticky top-0 w-full py-2">
        <div class="border-b justify-between flex items-center pb-2">

            <div class="flex items-center gap-2">
                 <h5 class="font-extrabold text-2xl">Conversas</h5>
            </div>

             <button> <!-- bootsrap icon -->
                <svg class="w-7 h-7"  xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                  </svg>
             </button>

          </div>

          {{-- Filtros --}} {{--click serve para caso os filtros sejam selecionados terem aquele style--}}
          <div class="flex gap-3 items-center overflow-hidden p-2 bg-white">

            <button @click="type='all'" :class="{'bg-blue-100 border-0 text-black':type=='all'}" class="inline-flex justify-center items-center rounded-full gap-x-1 text-xs font-medium px-3 lg:px-5 py-1  lg:py-2.5 border ">
                    Todos
            </button>
            <button @click="type='deleted'" :class="{'bg-blue-100 border-0 text-black':type=='deleted'}" class="inline-flex justify-center items-center rounded-full gap-x-1 text-xs font-medium px-3 lg:px-5 py-1  lg:py-2.5 border ">
                Deletados
            </button>

        </div>

    </header>


    <main id="chat-list" class=" overflow-y-scroll overflow-hidden grow  h-full relative " style="contain:content">

        {{-- chatlist --}}

        <ul class="p-2 grid w-full spacey-y-2">

            @if ($conversas) {{--ve se ele tem conversa para carregar com loop --}}

            @foreach ($conversas as $conversa) 

                <li 
                 id="conversa-{{$conversa->id}}" wire:key="{{$conversa->id}}" {{-- dar scroll para essa conversa quando ela for selecionada --}}
                class="py-3 hover:bg-gray-50 rounded-2xl dark:hover:bg-gray-700/70 transition-colors duration-150 flex gap-4 relative w-full cursor-pointer px- {{$conversa->id == $conversaSelecionada?->id ? 'bg-gray-100/90' : ''}}"> {{-- A conversa qur tiver o id igual da selecionada vai receber um destaque --}}
                    {{-- Na esquerda avatar, na direia os detalhes--}}
                    <a href="#" class="shrink-0">
                        <x-avatar  src="https://i1.sndcdn.com/artworks-LnLz9a9cajXZs4f7-ht8G0Q-t500x500.jpg"/>
                    </a>

                    <aside class="grid grid-cols-12 w-full">
                        <a href="{{route('chat', $conversa->id)}}" class="col-span-11 border-b pb-2 border-gray-200 relative overflow-hidden truncate leading-5 w-full flex-nowrap p-1">

                            {{-- Nome e Data--}}

                            <div class="flex justify-between w-full items-center">

                                <h6 class="truncate font-medium tracking-wider text-gray-900">
                                    {{$conversa->getDestinatario()->name}}
                                </h6>

                                <small class="text-gray-700">{{$conversa->mensagem?->last()?->created_at?->shortAbsoluteDiffForHumans()}}</small>         {{--Puxa o created at da ultima mensagem daquela conversa--}}

                            </div>

                            {{-- Corpo da mensagem --}}

                            <div class="flex gap-x-2 items-center">

                                @if ($conversa->mensagem?->last()?->remetente_id == Auth::id()) {{-- se a ultima mensagem da conversa (aquele que mostra no chat-list) foi enviada peo usuario logado; se foi, mostrar nenhum os risquinho, e não mostrar se for mensagem do outro, pois só ele pode ver o status  --}}

                                    {{-- Foi enviada pelo usuário e o destinatário leu--}}
                                    @if($conversa->ultimaMensagemFoiLidaPeloUsuario())

                                        {{-- Dois risquinhos--}}
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                                                <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z"/>
                                                <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z"/>
                                            </svg>
                                        </span>

                                    {{-- Foi enviada pelo usuário e o destinatário não leu--}}
                                    @else

                                        {{-- Um risquinho--}}
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                                                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                        </span>
                                    
                                    @endif

                                @endif

                                <p class="grow truncate text-sm font-[100]">
                                    {{$conversa->mensagem?->last()?->corpo ?? ""}}
                                </p>

                                {{-- Mensagens não lidas--}}
                                @if ($conversa->mensagensNaoLidasCount() > 0)

                                <span class="font-bold p-px px-2 text-xs shrink-0 rounded-full bg-blue-500 text-white">
                                    {{$conversa->mensagensNaoLidasCount()}}
                                </span>
                                
                                @endif

                            </div>

                        </a>

                        {{-- Dropdown opções: copiado do breeze--}}
                        <div class="col-span-1 flex flex-col text-center my-auto">

                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical w-7 h-7 text-gray-700" viewBox="0 0 16 16">
                                            <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                        </svg>
                                    </button>
                                </x-slot>
            
                                <x-slot name="content">
                                    <div class="w-full p-1">

                                        <button class="items-center gap-3 flex w-full px-4 py-2 text-left text-sm leading-5 text-gray-500 hover:bg-gray-100 transition-all duration-150 ease-in-out focus:outline-none focus:bg-gray-100">

                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                                </svg>
                                            </span>

                                            Ver perfil

                                        </button>  

                                        <button class="items-center gap-3 flex w-full px-4 py-2 text-left text-sm leading-5 text-gray-500 hover:bg-gray-100 transition-all duration-150 ease-in-out focus:outline-none focus:bg-gray-100">

                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                    <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>
                                                </svg>
                                            </span>

                                            Deletar

                                        </button>

                                </x-slot>
                            </x-dropdown>

                        </div>
                    </aside>


                </li>

            @endforeach

            @endif
        </ul>

    </main>

</div>