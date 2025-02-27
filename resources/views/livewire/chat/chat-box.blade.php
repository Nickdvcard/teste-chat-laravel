<div class="w-full overflow-hidden">

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
    
            <h6 class="font-bold truncate"> {{fake()->name()}} </h6>
        </div>
    </header>
    
    
    
    {{-- Body--}} {{-- @class pode condicionalmente carregar atributos css baseado no valor de variaveis--}}
    
    <main class="flex flex-col gap-3 p-2.5 overflow-y-auto  flex-grow overscroll-contain overflow-x-hidden w-full my-auto">
    
        <div @class([
            'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
        ]) >
    
        <div @class(['shrink-0',])>
    
            <x-avatar />
    
        </div>
    
    
            {{-- Corpo da mensagem--}}
    
        <div @class(['flex flex-wrap text-[15px]  rounded-xl p-2.5 flex flex-col text-black bg-[#f6f6f8fb]',
            'rounded-bl-none border  border-gray-200/40 '=>false,
            'rounded-br-none bg-blue-500/80 text-white'=>true  {{-- Condicionalmente muda o valor do balão de texto dependendo de quem o enviou --}}
        ])>
    
        <p class="whitespace-normal truncate text-sm md:text-base tracking-wide lg:tracking-normal">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Asperiores est laudantium nobis harum nemo quaerat, provident, nam molestiae error omnis nisi repudiandae illo! Nostrum quod earum officia, nobis quaerat suscipit!   
        </p>
    
        <div class="ml-auto flex gap-2">
    
            <p @class([
                'text-xs ',
                'text-gray-500'=>false,
                'text-white'=>true,
            ]) >
    
                4:20 am
    
            </p>
    
    
        {{-- Checar o status da mensagem, apenas ae ela foi eniada pelo usuário dessa session--}}
    
        <div>
    
        {{-- Dois Risquinhos--}}
    
        <span @class('text-gray-200')>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
            </svg>
        </span>
    
        {{-- Um risquinho --}}
        {{-- <span @class('text-gray-200')>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
            </svg>
        </span>
         --}}
    
        </div>
    
        </div>
    
    </main>
    
    
    
    {{-- Footer: mandar mensagem--}}

    <footer class="shrink-0 z-10 bg-white inset-x-0">

        <div class=" p-2 border-t">

            {{-- Form para enviar a mensagem--}}
            <form method="POST" autocapitalize="off">
                @csrf

                <input type="hidden" autocomplete="false" style="display:none">

                <div class="grid grid-cols-12">
                     <input 
                            type="text"
                            autocomplete="off"
                            autofocus
                            placeholder="Escreva aqui sua mensagem"
                            maxlength="1700"
                            class="col-span-10 bg-gray-100 border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg  focus:outline-none"
                     >

                     <button class="col-span-2" type='submit'>Send</button>

                </div>

            </form>

            @error('body')

            <p> {{$message}} </p>
                
            @enderror

        </div>

    </footer>

    </div>
    
</div>