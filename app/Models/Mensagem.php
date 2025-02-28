<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mensagem extends Model
{
    use HasFactory;

    protected $table = 'mensagens';

    protected $fillable = [
        "conversa_id",
        'remetente_id',
        'destinatario_id',
        'lido_em',
        'destinatario_deletado_em',
        'remetente_deletado_em',
        'corpo',
    ];

    protected $datas = ["lido_em",'destinatario_deletado_em','remetente_deletado_em'];

    public function conversa() {
        return $this->belongsTo(Conversa::class);
    }

    public function foiLido(): bool {
        return $this->lido_em != null;
    }

}
