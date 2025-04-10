<?php

namespace Alura\Leilao\Model;

use DomainException;

class Leilao
{
    private $lances;
    private $descricao;
    private $finalizado;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            throw new DomainException('Usuário não pode propor 2 lances consecutivos');
        }

        $totalLanceUsuario = $this->quantidadeLancesPorUsuario($lance->getUsuario());

        if ($totalLanceUsuario >= 5) {
            throw new DomainException('Usuário não pode propor mais de 5 lances por leilão');
        }

        $this->lances[] = $lance;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function estaFinalizado()
    {
        return $this->finalizado;
    }

    private function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = $this->lances[count($this->lances) - 1];
        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    public function getLances(): array
    {
        return $this->lances;
    }

    private function quantidadeLancesPorUsuario(Usuario $usuario): int
    {
        $totalLanceUsuario = array_reduce($this->lances, function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
            if ($lanceAtual->getUsuario() == $usuario) {
                return $totalAcumulado + 1;
            }

            return $totalAcumulado;
        }, 0);

        return $totalLanceUsuario;
    }
}
