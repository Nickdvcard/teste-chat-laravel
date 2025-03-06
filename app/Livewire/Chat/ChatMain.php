<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Conversa;
use App\Models\Mensagem;
use Illuminate\Support\Facades\Auth;

class ChatMain extends Component
{

    public $query; //parametro que vem da rota(link)
    public $conversaSelecionada;

    public function mount() { //executa logo que chat-main é chamado

        $this->conversaSelecionada = Conversa::findOrFail($this->query); //encontra a conversa selecionado, baseado no id da conversa passado na rota

        //dd($this->conversaSelecionada);

        //marcar mensagens do destinatario como lida 
        //marca como lida as mensagens não lidas, dessa conversa, que tem o usuário autenticado como destinatário
        Mensagem::where('conversa_id', $this->conversaSelecionada->id)->where('destinatario_id', Auth::id())->whereNull('lido_em')->update(['lido_em' => now()]);
    }

    public function render()
    {
        return view('livewire.chat.chat-main');
    }
}
