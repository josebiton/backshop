<?php

use CodeIgniter\Test\CIUnitTestCase;

final class HealthTest extends CIUnitTestCase {


  //  public function testAgregarProductoInventario() : void
    // {
     //   $inventario = new MockInventario();
       // $producto = new MockProducto("Manzana", 1.5);
       // $inventario->agregarProducto($producto, 10);

       // $this->assertTrue($inventario->estaProductoDisponible("Manzana"));
    //}

    /*public function testReducirCantidadProducto() : void*/
    /* {
        $inventario = new MockInventario();
        $producto = new MockProducto("Manzana", 1.5);
        $inventario->agregarProducto($producto, 10);
        $inventario->reducirCantidadProducto("Manzana", 5);

        $this->assertTrue($inventario->estaProductoDisponible("Manzana"));
    }*/

    /*public function testAgregarProductoCarrito() {
        $carrito = new MockCarritoDeCompras();
        $producto = new MockProducto("Manzana", 1.5);
        $carrito->agregarProducto($producto);

        $this->assertCount(1, $carrito->productos);
    }

    public function testCalcularTotal() {
        $carrito = new MockCarritoDeCompras();
        $carrito->agregarProducto(new MockProducto("Manzana", 1.5));
        $carrito->agregarProducto(new MockProducto("Plátano", 0.5));

        $this->assertEquals(2.0, $carrito->calcularTotal());
    }

    public function testAplicarDescuento() {
        $carrito = new MockCarritoDeCompras();
        $carrito->agregarProducto(new MockProducto("Manzana", 2.0));
        $carrito->aplicarDescuento(0.1);

        $this->assertEquals(1.8, $carrito->calcularTotal());
    }
}*/

class MockProducto {
    public $nombre;
    public $precio;

    public function __construct($nombre, $precio) {
        $this->nombre = $nombre;
        $this->precio = $precio;
    }
}

class MockInventario {
    private $productos = [];

    public function agregarProducto(MockProducto $producto, $cantidad) {
        $this->productos[$producto->nombre] = ['producto' => $producto, 'cantidad' => $cantidad];
    }

    public function reducirCantidadProducto($nombre, $cantidad) {
        if (isset($this->productos[$nombre])) {
            $this->productos[$nombre]['cantidad'] -= $cantidad;
            if ($this->productos[$nombre]['cantidad'] <= 0) {
                unset($this->productos[$nombre]);
            }
        }
    }

    public function estaProductoDisponible($nombre) {
        return isset($this->productos[$nombre]) && $this->productos[$nombre]['cantidad'] > 0;
    }
}

class MockCarritoDeCompras {
    public $productos = [];
    private $descuento = 0.0;

    public function agregarProducto(MockProducto $producto) {
        $this->productos[] = $producto;
    }

    public function eliminarProducto($productoNombre) {
        foreach ($this->productos as $key => $producto) {
            if ($producto->nombre === $productoNombre) {
                unset($this->productos[$key]);
            }
        }
    }

    public function calcularTotal() {
        $total = array_reduce($this->productos, function ($carry, $producto) {
            return $carry + $producto->precio;
        }, 0);
        return $total * (1 - $this->descuento);
    }

    public function aplicarDescuento($descuento) {
        $this->descuento = $descuento;
    }
}
