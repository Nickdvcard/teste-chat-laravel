<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ChatList extends Component
{   

    public $conversaSelecionada;

    public function render()
    {
        $user = Auth::user();

        return view('livewire.chat.chat-list', [
            "conversas"=>$user->conversa()->latest('updated_at')->get() //carrega as conversas mais novas do chat primeiro, na sidebar do chat
        ]);
    }
}
