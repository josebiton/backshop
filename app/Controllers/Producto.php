<?php 
namespace App\Controllers;

use App\Models\ProductoDetalleModel;
use App\Models\ProductoImagenModel;
use App\Models\ProductoModel;


class Producto extends BaseController{

    public function index()
    {
        try {
          //  $productoModel = new ProductoModel();
            $productoModel = new ProductoDetalleModel();
            $productos = $productoModel->getProductosConDetalle();

            foreach ($productos as &$producto) {
                $imagenes = explode(',', $producto['imagenes']);
                unset($producto['imagenes']);
                $producto['imagenes'] = $imagenes;
            }
            
          //  return $this->response->setJSON(['Data' => $productos]);
            return $this->response->setJSON($productos);
          //  return $this->response->setJSON($productos);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error interno' . $e->getMessage()]);
        }
    }
    
    // Lista productos por categoria 

    public function getProductosPorCategoria($categoria_id){
        try {
            $productoModel = new ProductoDetalleModel();
            $productos = $productoModel->getProductosPorCategoria($categoria_id);

            foreach ($productos as &$producto) {
                $imagenes = explode(',', $producto['imagenes']);
                unset($producto['imagenes']);
                $producto['imagenes'] = $imagenes;
            }
            
          //  return $this->response->setJSON(['Data' => $productos]);
            return $this->response->setJSON($productos);
          //  return $this->response->setJSON($productos);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error interno' . $e->getMessage()]);
        }
    }

    // Lista productos por id 

    public function ProductosPorId($id){
        try {
            $productoModel = new ProductoDetalleModel();
            $producto = $productoModel->getProductosPorId($id);

            foreach ($producto as &$producto) {
              $imagenes = explode(',', $producto['imagenes']);
              unset($producto['imagenes']);
              $producto['imagenes'] = $imagenes;
          }

            return $this->response->setJSON($producto);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error interno' . $e->getMessage()]);
        }
    }


  

    public function create()
    {
        $request = $this->request;


        // Lógica para subir imágenes
        $imagenModel = new ProductoImagenModel();
    
        $imagen1 = $request->getFile('imagen1');
        $imagen2 = $request->getFile('imagen2');
        $imagen3 = $request->getFile('imagen3');
    
        $uploadedImages = [];
    
        if ($imagen1 && $imagen1->isValid() && !$imagen1->hasMoved()) {
            $uploadedImages[] = $imagen1;
        }
    
        if ($imagen2 && $imagen2->isValid() && !$imagen2->hasMoved()) {
            $uploadedImages[] = $imagen2;
        }
    
        if ($imagen3 && $imagen3->isValid() && !$imagen3->hasMoved()) {
            $uploadedImages[] = $imagen3;
        }
    
        if (empty($uploadedImages)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Debes proporcionar al menos una foto y máximo tres.']);
        }
    
        function generateSlug($string) {
            // Reemplaza espacios con guiones bajos
            $slug = str_replace(' ', '-', $string);
            
            // Convierte la cadena a minúsculas
            $slug = strtolower($slug);
            
            // Elimina caracteres especiales
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        
            return $slug;
        }

        $detalleModel = new ProductoDetalleModel();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'categoria_id' => 'required',
            'unidad_id' => 'required',
            'precio' => 'required',
            'stock' => 'required',
        ]);

        if ($validation->withRequest($this->request)->run() === false) {
            return $this->response->setStatusCode(400)->setJSON(['error' => $validation->getErrors()]);
        }

        // Lógica para validar y guardar los datos del producto
        $productoModel = new ProductoModel();
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|max_length[255]',
            'descripcion' => 'required|max_length[255]',
            'marca_id' => 'required'
        ]);
    
        
        if ($validation->withRequest($this->request)->run() === false) {
            return $this->response->setStatusCode(400)->setJSON(['error' => $validation->getErrors()]);
        }
    
        // Datos válidos, procedemos a guardar el producto
        $data = [
            'nombre' => $request->getPost('nombre'),
            'descripcion' => $request->getPost('descripcion'),
            'marca_id' => $request->getPost('marca_id'),
            'estado' => "A",
        ];
    
        $productoModel->insert($data);
        $productoId = $productoModel->getInsertID();


        

        $nombreProducto = $request->getPost('nombre');
        $slug = generateSlug($nombreProducto);


        $data = [
            'slug' => $slug,
            'producto_id' => $productoId,
            'empresa_id' => 1,
            'categoria_id' => $request->getPost('categoria_id'),
            'unidad_id' => $request->getPost('unidad_id'),
            'precio' => $request->getPost('precio'),
            'stock' => $request->getPost('stock'),
            'estado' => "A",
        ];

        $detalleModel->insert($data);
        $productoDetalleId = $detalleModel->getInsertID();

    
        foreach ($uploadedImages as $imagen) {
           // $extension = $imagen->getClientExtension();
            $extension = 'png';
            $newName = date('Ymd') . '-PR' . mt_rand(1000, 9999) . '.' . $extension;
            $imagen->move(ROOTPATH . 'public/imagenes/productos', $newName);
    
            $data = [
                'producto_detalle_id' => $productoDetalleId,
                'descripcion' => $request->getPost('descripcion'),
                'ruta' => 'productos/' . $newName,
                'estado' => "A"
            ];
    
            $imagenModel->insert($data);
        }
    
        return $this->response->setStatusCode(201)->setJSON(['message' => 'Producto creado con éxito.']);
    }
    
}