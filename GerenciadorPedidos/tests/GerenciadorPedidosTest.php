<?php
use PHPUnit\Framework\TestCase;

class GerenciadorPedidosTest extends TestCase {

    public function testAdicionarItem() {
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->adicionarItem("Produto 1", 2, 50);
        $itens = $pedido->listarItens();

        $this->assertCount(1, $itens);
        $this->assertEquals("Produto 1", $itens[0]['produto']);
        $this->assertEquals(2, $itens[0]['quantidade']);
        $this->assertEquals(50, $itens[0]['preco_unitario']);
    }

    public function testAplicarDescontoValido() {
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->aplicarDesconto("DESC10");

        $reflection = new ReflectionClass($pedido);
        $desconto = $reflection->getProperty('desconto');
        $desconto->setAccessible(true);
        $this->assertEquals(0.10, $desconto->getValue($pedido));
    }

    public function testAplicarDescontoInvalido() {
        $this->expectException(InvalidArgumentException::class);
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->aplicarDesconto("INVALIDO");
    }

    public function testCalcularTotalSemDesconto() {
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->adicionarItem("Produto 1", 2, 50);
        $pedido->adicionarItem("Produto 2", 1, 100);

        $total = $pedido->calcularTotal();
        $this->assertEquals(200, $total);
    }

    public function testCalcularTotalComDesconto() {
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->adicionarItem("Produto 1", 2, 50);
        $pedido->adicionarItem("Produto 2", 1, 100);
        $pedido->aplicarDesconto("DESC10");

        $total = $pedido->calcularTotal();
        $this->assertEquals(180, $total);
    }

    public function testPedidoValido() {
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->adicionarItem("Produto 1", 2, 50);
        
        $pedido->validarPedido();
        $this->assertTrue(true);
    }

    public function testPedidoInvalidoSemItens() {
        $this->expectException(RuntimeException::class);
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->validarPedido();
    }

    public function testPedidoComTotalNegativoOuZero() {
        $this->expectException(RuntimeException::class);
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->adicionarItem("Produto 1", 0, 50);
        $pedido->validarPedido();
    }

    public function testConfirmacaoPedidoValido() {
        $pedido = new GerenciadorPedidos("Cliente Teste");
        $pedido->adicionarItem("Produto 1", 2, 50);
        
        $this->assertTrue($pedido->confirmarPedido());
    }
}

?>
