<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversa;

class ChatList extends Component
{   
    protected $listeners = ['refresh' => 'carregarConversas'];

    public $conversaSelecionada;
    public $query;

    public function atualizar() {

        $this->dispatch("refresh");
    }

    public function arquivadoPorUsuario($conversaId) {

        //dd($conversaId, decrypt($conversaId));

        $userId = Auth::id();

        $conversaId = decrypt($conversaId);

        // Obter a conversa no banco de dados
        $conversa = Conversa::findOrFail($conversaId);

        // Determinar quem arquivou: se o usuário autenticado é o destinatário ou remetente
        if ($conversa->destinatario_id === $userId) {
            // Se o destinatário arquivou
            $conversa->destinatario_arquivou = true;
        } elseif ($conversa->remetente_id === $userId) {
            // Se o remetente arquivou
            $conversa->remetente_arquivou = true;
        }

        $conversa->save();

        $this->atualizar();
    }

    public function mostrarTodasAsConversas() {

        //dump("mostrar todas as conversas");

        $userId = Auth::id();
    
        // Obter conversas onde o usuário é remetente ou destinatário, e não arquivou a conversa
        $conversas = Conversa::where(function ($query) use ($userId) {
            // Conversas em que o usuário é remetente e não arquivou
            $query->where('remetente_id', $userId)
                  ->where('remetente_arquivou', 0);
        })
        ->orWhere(function ($query) use ($userId) {
            // Conversas em que o usuário é destinatário e não arquivou
            $query->where('destinatario_id', $userId)
                  ->where('destinatario_arquivou', 0);
        })
        ->orderBy('ultima_mensagem', 'desc')
        ->get();
    
        return view('livewire.chat.chat-list', [
            'conversas' => $conversas,
        ]);
    }

    public function mostrarConversasArquivadas() {

        //dump("mostrar todas as arquivadas");

        $userId = Auth::id();

        // Buscar todas as conversas onde o usuário arquivou como remetente ou destinatário
        $conversas = Conversa::where(function ($query) use ($userId) {
            // Conversas onde o usuário é remetente e arquivou
            $query->where('remetente_id', $userId)
                  ->where('remetente_arquivou', 1);
        })
        ->orWhere(function ($query) use ($userId) {
            // Conversas onde o usuário é destinatário e arquivou
            $query->where('destinatario_id', $userId)
                  ->where('destinatario_arquivou', 1);
        })
        ->orderBy('ultima_mensagem', 'desc')
        ->get();
    
        return view('livewire.chat.chat-list', [
            'conversas' => $conversas,
        ]);
    }

    public function render()
    {
        $userId = Auth::id();
    
        // Obter conversas onde o usuário é remetente ou destinatário, e não arquivou a conversa
        $conversas = Conversa::where(function ($query) use ($userId) {
            // Conversas em que o usuário é remetente e não arquivou
            $query->where('remetente_id', $userId)
                  ->where('remetente_arquivou', 0);
        })
        ->orWhere(function ($query) use ($userId) {
            // Conversas em que o usuário é destinatário e não arquivou
            $query->where('destinatario_id', $userId)
                  ->where('destinatario_arquivou', 0);
        })
        ->orderBy('ultima_mensagem', 'desc')
        ->get();
    
        return view('livewire.chat.chat-list', [
            'conversas' => $conversas,
        ]);
    }
    
}
