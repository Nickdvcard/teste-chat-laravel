<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Conversa extends Model
{
    use HasFactory;

    protected $fillable = [
        "remetente_id",
        "destinatario_id",
        "remetente_arquivou",
        "destinatario_arquivou",
    ];

    public function mensagem() {
        return $this->hasMany(Mensagem::class);
    }

    public function getDestinatario() {

        if ($this->remetente_id == Auth::id()) { //pega a outra pessoa envolvida na conversa; se a pessoa autenticada é o remetente, significa que a outra é o destinatário, e vice-versa
            
            return User::firstWhere('id', $this->destinatario_id);
        } else {
            
            return User::firstWhere('id', $this->remetente_id);
        }
    }

    public function ultimaMensagemFoiLidaPeloUsuario() {
        
        $user = Auth::user();
        $ultimaMensagem = $this->mensagem()->latest()->first();

        //ve se a ultima mensagem foi lida pelo outro usuario que a recebeu e se quem enviou foi o presente usuario logado
        if ($ultimaMensagem) {
            return $ultimaMensagem->lido_em !== null && $ultimaMensagem->remetente_id === $user->id;
        } 

        else {
            return false; //se não tiver ultima mensagem, retorna falso
        }
    }

    public function mensagensNaoLidasCount(): int {

        //Pega as mensagens dessa conversa, que tem o usuário logado como destinatário, que não fora lidas, e as contam
        $mensagensNaoLidas = Mensagem::where('conversa_id', "=", $this->id)->where('destinatario_id', Auth::id())->whereNull('lido_em')->count();
        return $mensagensNaoLidas;
    }
}
