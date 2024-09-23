<?php

class ContaBancaria {
    private $cliente;
    private $historicoTransacoes;
    private $saldo;

    public function __construct($cliente, $saldoinicial) {
        $this->cliente = $cliente;
        $this->saldo = $saldoinicial;
        $this->historicoTransacoes = [];
        $this->addTransacao('Depósito Inicial', $saldoinicial);
    }

    private function addTransacao($tipo, $valor) {
        $this->historicoTransacoes[] = [
            'tipo' => $tipo,
            'valor' => $valor,
            'data' => date('Y-m-d H:i:s')
        ];
    }

    public function getHistoricoTransacoes() {
        return $this->historicoTransacoes;
    }

    public function transferir($valor, ContaBancaria $contaDestino) {
        if ($this->saldo >= $valor) {
            $this->saque($valor);
            $contaDestino->deposito($valor);
            $this->addTransacao('Transferência Enviada', $valor);
            $contaDestino->addTransacao('Transferência Recebida', $valor);
        } else {
            throw new Exception('Saldo insuficiente para transferência.');
        }
    }

    public function saque($valor) {
        if ($this->saldo >= $valor) {
            $this->saldo -= $valor;
            $this->addTransacao('Saque', $valor);
        } else {
            throw new Exception('Saldo insuficiente para saque.');
        }
    }

    public function getSaldo() {
        return $this->saldo;
    }

    public function calcularJuros($taxa) {
        $juros = $this->saldo * ($taxa / 100);
        $this->saldo += $juros;
        $this->addTransacao('Juros', $juros);
    }

    public function deposito($valor) {
        $this->saldo += $valor;
        $this->addTransacao('Depósito', $valor);
    }

    public function getCliente() {
        return $this->cliente;
    }
}
?>