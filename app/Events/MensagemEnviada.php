<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

//ShouldBroadcastNow é instantâneo, enquanto ShouldBroadcast leva um tempo para ser transmitido
class MensagemEnviada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensagem;
    public $destinatarioId;

    /**
     * Create a new event instance.
     */
    public function __construct($mensagem) //recebe a mensagem criada do ChatBox
    {
        $this->mensagem = $mensagem;
        $this->destinatarioId = $mensagem->destinatario_id; // Adiciona o ID do destinatário
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        //dd($this->mensagem);
        return [
            new PrivateChannel('conversa.' . $this->mensagem->conversa_id), //canal privado para o destinatário
            new PrivateChannel('usuario.' . $this->mensagem->destinatario_id) // Canal do usuário
        ];
    }

    public function broadcastWith()
    {
        return [
            'mensagem' => $this->mensagem,
            'destinatarioId' => $this->destinatarioId, // Inclui o ID do destinatário na transmissão
        ];
    }
}
