<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ChatList extends Component
{   
    protected $listeners = ['refresh' => 'carregarConversas'];

    public $conversaSelecionada;
    public $query;

    public function atualizar() {

        $this->dispatch("refresh");
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.chat.chat-list', [
            "conversas"=>$user->conversa()->orderBy('updated_at', 'desc')->get() //carrega as conversas mais novas do chat primeiro, na sidebar do chat
        ]);
    }
}
