<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Conversa;
use Illuminate\Support\Facades\Auth;

//backend do componente livewire users

class Users extends Component
{

    public function message ($userId) {

        $usuarioAutenticadoId = Auth::id();

        //dd($userId, $usuarioAutenticadoId);

        //ver se essa conversa com usuário já existe, para não criar duplicado (ver em "ambos os sentidos" de quem recebe e envia)

        $existeConversa = Conversa::where( function ($query) use($usuarioAutenticadoId, $userId){ 
            $query->where('remetente_id', $usuarioAutenticadoId)->where('destinatario_id', $userId);
        })->orWhere( function ($query) use($usuarioAutenticadoId, $userId){
            $query->where('remetente_id', $userId)->where('destinatario_id', $usuarioAutenticadoId);
        })->first();


        //se existir, encaminha para tal conversa
        if ($existeConversa) { 
            return redirect()->route('chat', ['query'=>$existeConversa->id]); //retorna o id de tal conversa
        }


        //se não existir, cria a conversa eredireciona até ela

        $conversaCriada = Conversa::create([
            "remetente_id" => $usuarioAutenticadoId,
            "destinatario_id" => $userId,
        ]);

        return redirect()->route('chat', ['query'=>$conversaCriada->id]);
    }

    public function render() {

        $todosOutrosUsers = User::where('id', "!=", Auth::id()); //tomando cuidado para não se incluir nessa lista

        //dd($todosOutrosUsers->get());

        return view('livewire.users', ['users'=>$todosOutrosUsers->paginate(6)]); //obtém todos os usuários baseado no model de User, e depois manda para a blade dentro de uma variável
    }
}
