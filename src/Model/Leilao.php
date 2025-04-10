<?php

namespace Alura\Leilao\Model;

class Leilao
{
    private $lances;
    private $descricao;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            return;
        }

        $totalLanceUsuario = $this->quantidadeLancesPorUsuario($lance->getUsuario());

        if ($totalLanceUsuario >= 5) {
            return;
        }

        $this->lances[] = $lance;
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
