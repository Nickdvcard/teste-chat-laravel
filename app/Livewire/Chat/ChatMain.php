<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Conversa;

class ChatMain extends Component
{

    public $query; //parametro que vem da rota(link)
    public $conversaSelecionada;

    public function mount() { //executa logo que chat-main é chamado

        $this->conversaSelecionada = Conversa::findOrFail($this->query); //encontra a conversa selecionado, baseado no id da conversa passado na rota

        //dd($this->conversaSelecionada);
    }

    public function render()
    {
        return view('livewire.chat.chat-main');
    }
}
