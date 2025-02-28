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
}
