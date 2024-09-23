<?php

use PHPUnit\Framework\TestCase;

class ContaBancariaTest extends TestCase {
    private $conta;

    protected function setUp(): void {
        $this->conta = new ContaBancaria("Cliente Teste", 1000);
    }

    public function testDepositoAtualizaSaldo() {
        $this->conta->deposito(500);
        $this->assertEquals(1500, $this->conta->getSaldo());
    }

    public function testDepositoQuantiaNegativaOuZero() {
        $saldoAnterior = $this->conta->getSaldo();
        $this->conta->deposito(0);
        $this->assertEquals($saldoAnterior, $this->conta->getSaldo());

        $this->conta->deposito(-100);
        $this->assertEquals($saldoAnterior, $this->conta->getSaldo());
    }

    public function testDepositoRegistroHistorico() {
        $this->conta->deposito(500);
        $historico = $this->conta->getHistoricoTransacoes();
        $ultimaTransacao = end($historico);
        $this->assertEquals('Depósito', $ultimaTransacao['tipo']);
        $this->assertEquals(500, $ultimaTransacao['valor']);
    }

    public function testSaqueAtualizaSaldo() {
        $this->conta->saque(500);
        $this->assertEquals(500, $this->conta->getSaldo());
    }

    public function testSaqueQuantiaMaiorQueSaldo() {
        $this->expectException(Exception::class);
        $this->conta->saque(1500);
    }

    public function testSaqueQuantiaNegativaOuZero() {
        $saldoAnterior = $this->conta->getSaldo();
        $this->expectException(Exception::class);
        $this->conta->saque(0);
        $this->expectException(Exception::class);
        $this->conta->saque(-100);
        $this->assertEquals($saldoAnterior, $this->conta->getSaldo());
    }

    public function testSaqueRegistroHistorico() {
        $this->conta->saque(500);
        $historico = $this->conta->getHistoricoTransacoes();
        $ultimaTransacao = end($historico);
        $this->assertEquals('Saque', $ultimaTransacao['tipo']);
        $this->assertEquals(500, $ultimaTransacao['valor']);
    }

    public function testTransferirAtualizaSaldos() {
        $contaDestino = new ContaBancaria("Cliente Destino", 500);
        $this->conta->transferir(300, $contaDestino);
        $this->assertEquals(700, $this->conta->getSaldo());
        $this->assertEquals(800, $contaDestino->getSaldo());
    }

    public function testTransferirQuantiaMaiorQueSaldo() {
        $contaDestino = new ContaBancaria("Cliente Destino", 500);
        $this->expectException(Exception::class);
        $this->conta->transferir(1500, $contaDestino);
    }

    public function testTransferirRegistroHistorico() {
        $contaDestino = new ContaBancaria("Cliente Destino", 500);
        $this->conta->transferir(300, $contaDestino);

        $historicoOrigem = $this->conta->getHistoricoTransacoes();
        $ultimaTransacaoOrigem = end($historicoOrigem);
        $this->assertEquals('Transferência Enviada', $ultimaTransacaoOrigem['tipo']);
        $this->assertEquals(300, $ultimaTransacaoOrigem['valor']);

        $historicoDestino = $contaDestino->getHistoricoTransacoes();
        $ultimaTransacaoDestino = end($historicoDestino);
        $this->assertEquals('Transferência Recebida', $ultimaTransacaoDestino['tipo']);
        $this->assertEquals(300, $ultimaTransacaoDestino['valor']);
    }

    public function testCalcularJuros() {
        $this->conta->calcularJuros(10);
        $this->assertEquals(1100, $this->conta->getSaldo());
    }

    public function testCalcularJurosTaxaNegativaOuZero() {
        $saldoAnterior = $this->conta->getSaldo();
        $this->conta->calcularJuros(0);
        $this->assertEquals($saldoAnterior, $this->conta->getSaldo());

        $this->conta->calcularJuros(-5);
        $this->assertEquals($saldoAnterior, $this->conta->getSaldo());
    }

    public function testCalcularJurosRegistroHistorico() {
        $this->conta->calcularJuros(10);
        $historico = $this->conta->getHistoricoTransacoes();
        $ultimaTransacao = end($historico);
        $this->assertEquals('Juros', $ultimaTransacao['tipo']);
        $this->assertEquals(100, $ultimaTransacao['valor']);
    }

    public function testGetSaldo() {
        $this->conta->deposito(500);
        $this->conta->saque(200);
        $this->assertEquals(1300, $this->conta->getSaldo());
    }

    public function testGetCliente() {
        $this->assertEquals("Cliente Teste", $this->conta->getCliente());
    }

    public function testGetHistoricoTransacoes() {
        $this->conta->deposito(500);
        $this->conta->saque(200);
        $historico = $this->conta->getHistoricoTransacoes();

        $this->assertCount(3, $historico);
        $this->assertEquals('Depósito', $historico[1]['tipo']);
        $this->assertEquals(500, $historico[1]['valor']);
        $this->assertEquals('Saque', $historico[2]['tipo']);
        $this->assertEquals(200, $historico[2]['valor']);
    }
}

?>
