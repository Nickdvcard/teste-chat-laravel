<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversa.{conversaId}', function ($user, $conversaId) {
    // Busca a conversa no banco de dados
    $conversa = \App\Models\Conversa::find($conversaId);

    // Verifica se o usuário autenticado é o remetente ou destinatário da conversa
    return $conversa && (
        (int) $user->id === (int) $conversa->destinatario_id ||
        (int) $user->id === (int) $conversa->remetente_id
    );
});


