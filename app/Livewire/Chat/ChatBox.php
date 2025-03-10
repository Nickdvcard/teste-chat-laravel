<?php

namespace App\Livewire\Chat;

use App\Events\MensagemEnviada;
use Livewire\Component;
use App\Models\Mensagem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ChatBox extends Component
{

    public $conversaSelecionada;
    public $body;
    public $mensagensCarregadas;

    public $variavelPaginacao = 10; //quantidade de mensagens em uma conversa carregadas por vez, para não carregar todas de uma vez e causar lentidão

    public $listeners = ['carregarMaisMensagens'];

    public function listenParaMensagem($event) {

        if (Auth::id() !== (int) $event['destinatarioId']) {
            return; // Ignora a mensagem se o usuário não for o destinatário
        }

        dd('aaaaaaaaaaaaaa');
    }

    public function carregarMaisMensagens() {

        //só pra ver se chama a função carregarMaisMensagens desencadeada na blade ao dar o scroll pro topo
        //dd("chamou");

        //aumentar a qunatidade de mensagens carregadas por vez
        $this->variavelPaginacao += 10;

        //chamar carregarMensagens() para carregar mais mensagens agora que o variavelPaginação foi aumentado
        $this->carregarMensagens();   

        //atualizar a altura do chat
        $this->dispatch("atualiza-altura-chat");
    }

    public function carregarMensagens() {

        //contar quantas mensagens estão sendo carregadas de uma vez
        $count = Mensagem::where('conversa_id', $this->conversaSelecionada->id)->count();

        //carrega as mensagens cujo conversa_id é igual ao id da conversa presentemente selecionada
        //skip ignora as mensagens mais antigas, take limita o número de mensagens carregadas
        //carrega as mensagens mais recentes, de acordo com a quantidade de mensagens carregadas por vez determinada em variavelPaginacao
        $this->mensagensCarregadas = Mensagem::where('conversa_id', $this->conversaSelecionada->id)->skip($count - $this->variavelPaginacao)->take($this->variavelPaginacao)->get(); 
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

        $this->dispatch('scroll-bottom')->to(ChatList::class);

        broadcast(new MensagemEnviada($mensagemCriada))->toOthers();

        // dd($mensagemCriada);

        // dd($this->body);
    }

    public function mount() {

        $this->carregarMensagens(); //carregar as mensagens quando entra na conversa

        if ($this->conversaSelecionada) {
            $this->listeners["echo-private:conversa.{$this->conversaSelecionada->id},MensagemEnviada"] = 'listenParaMensagem';
        }

    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}

