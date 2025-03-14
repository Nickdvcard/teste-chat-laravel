<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//canal para eventos que devem ocorrer quando os dois usuáris está na mesma conversa
//1. O usuário remetente envia uma mensagem -> puxa ela pra tela do destinatário
//2. O usuário destinatário lê a mensagem na hora -> marca como lida na tela do remetente
//3. O usuário destinatário lê a mensagem ao entrar de novo na conversa -> marca como lida na tela do remetente
Broadcast::channel('conversa.{conversaId}', function ($user, $conversaId) {
    // Busca a conversa no banco de dados
    $conversa = \App\Models\Conversa::find($conversaId);

    // Verifica se o usuário autenticado é o remetente ou destinatário da conversa
    return $conversa && (
        (int) $user->id === (int) $conversa->destinatario_id ||
        (int) $user->id === (int) $conversa->remetente_id
    );
});

//canal para eventos que deve ocorrer mesmo que um dos usuários não esteja na conversa
//1. O usuário remetente envia uma mensagem e o destinatário está em outra conversa -> atualiza a sidebar do destinatário
Broadcast::channel('usuario.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
