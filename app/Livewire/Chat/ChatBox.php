<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class ChatBox extends Component
{

    public $conversaSelecionada;

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
