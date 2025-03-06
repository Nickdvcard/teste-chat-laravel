<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Mensagem;
use Illuminate\Support\Facades\Auth;

class ChatBox extends Component
{

    public $conversaSelecionada;
    public $body;
    public $mensagensCarregadas;

    public function carregarMensagens() {

        $this->mensagensCarregadas = Mensagem::where('conversa_id', $this->conversaSelecionada->id)->get(); //carrega as mensagens cujo conversa_id é igual aoid da conversa presentemente selecionada
    }

    public function enviarMensagem() {

        $this->validate(['body' => 'required|string']);

        $mensagemCriada = Mensagem::create([
            "conversa_id" => $this->conversaSelecionada->id,
            "remetente_id" => Auth::id(),
            "destinatario_id" => $this->conversaSelecionada->getDestinatario()->id,
            "corpo" => $this->body,
        ]);

        $this->reset('body');

        //rolar a telapra baixo quando enviar nova mensagem
        $this->dispatch("scroll-bottom");

        //mandar a mensagem enviada para o chat (empurra ela junto das outras carregadas)
        $this->mensagensCarregadas->push($mensagemCriada);

        //sinalizar que a conversa foi atualizada
        $this->conversaSelecionada->updated_at = now();
        $this->conversaSelecionada->save();

        //atualizar a chatlist após enviar mensagem 
        $this->dispatch('refresh')->to(ChatList::class);

        // dd($mensagemCriada);

        // dd($this->body);
    }

    public function mount() {

        $this->carregarMensagens(); //carregar as mensagens quando entra na conversa
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}
