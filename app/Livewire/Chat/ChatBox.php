<?php

namespace App\Livewire\Chat;

use App\Events\LerMensagensAoEntrarConversa;
use App\Events\MensagemEnviada;
use App\Events\MensagemLida;
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

    public $listeners = ['carregarMaisMensagens', 'mensagemLida'];

    public function atualizar() {

        $this->dispatch("refresh");
    }

    public function listenParaMensagem($event) {

        // if (Auth::id() !== (int) $event['destinatarioId']) {
        //     return; // Ignora a mensagem se o usuário não for o destinatário
        // }

        $this->dispatch("scroll-bottom");

        // Acessar o ID da mensagem
        $mensagemId = $event['mensagem']['id'];

        // Acessar a mensagem completa
        $novaMensagem = Mensagem::find($mensagemId);

        $this->mensagensCarregadas->push($novaMensagem);

        $novaMensagem->lido_em = now();
        $novaMensagem->save();

        //informar ao outro usuário que a mensagem foi lida
        //naõ faz lsitem que nem o ouro pois tem que mostrar a notificação no front aí é melhor chamar por lá
        //O método toOthers é usado em conjunto com o método broadcast para enviar um evento de broadcast para todos os outros usuários conectados ao canal, exceto o usuário que disparou o evento.
        broadcast(new MensagemLida($novaMensagem->conversa_id))->toOthers();
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

        if (empty(trim($this->body))) {
            return;
        }

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

        // Marcar mensagens como lidas
        $this->marcarMensagensComoLidasAoEntrarNaConversa();
    }

    public function marcarMensagensComoLidasAoEntrarNaConversa() {
        // Verifica se há mensagens não lidas para o usuário logado nesta conversa

        Mensagem::where('conversa_id', $this->conversaSelecionada->id)
        ->where('destinatario_id', Auth::id()) // Supondo que o ID do destinatário seja armazenado
        ->whereNull('lido_em')  // Garante que só as não lidas sejam alteradas
        ->update(['lido_em' => now()]); // Atualiza para 'lida'
    
        broadcast(new MensagemLida($this->conversaSelecionada->id))->toOthers();
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }
}

