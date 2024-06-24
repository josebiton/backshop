<?php

namespace App\Controllers;

use App\Models\CategoriaModel;


class Categoria extends BaseController
{


    public function index()
    {
        try {
            $categoriaModel = new CategoriaModel();
            $categorias = $categoriaModel->Where('estado', '1')->findAll();
           //return $this->response->setJSON(['Data' => $categorias]);
          return $this->response->setJSON($categorias);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error interno' . $e->getMessage()]);
        }
    }


    public function show($id){
        try {
            $categoriaModel = new CategoriaModel();
            $categoria = $categoriaModel->where('estado', '1')->find($id);
            if (!$categoria) {
                return $this->response->setJSON(['error' => 'Categoría no encontrada.']);
            }
            return $this->response->setJSON($categoria);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error interno' . $e->getMessage()]);
        }
    }


    public function store()
{
    try {
        // Obtén los datos enviados por la solicitud POST
        $requestData = $this->request->getPost();

        // Valida los datos utilizando las reglas de validación definidas en el modelo
        $categoriaModel = new CategoriaModel();

        if (!$categoriaModel->validate($requestData)) {
            // Hubo errores de validación
            return $this->response->setJSON(['error' => $categoriaModel->errors()]);
        } 

        // Capturar la imagen de la categoría y guardarla en el directorio de imágenes
        $file = $this->request->getFile('imagen');

        if ($file->isValid() && !$file->hasMoved()) {
            $extension = 'png';  // Cambiar la extensión de la imagen si es necesario
            $newName = date('Ymd') . '-CT' . mt_rand(1000, 9999) . '.' . $extension;
            $file->move(ROOTPATH . 'public/imagenes/categorias', $newName);

            // Inserta los datos en la base de datos
            $categoriaData = [
                'nombre' => $requestData['nombre'],
                'imagen' => 'categorias/'.$newName,
                'estado' => 1, // Categoría activa por defecto
            ];

            if ($categoriaModel->insert($categoriaData)) {
                // Verifica si la inserción fue exitosa
                return $this->response->setJSON(['status' => 'success', 'message' => 'Categoría registrada con éxito']);
            } else {
                return $this->response->setJSON(['error' => 'No se pudo registrar la categoría.']);
            }
        } else {
            return $this->response->setJSON(['error' => 'La imagen no es válida o no se pudo guardar.']);
        }
    } catch (\Exception $e) {
        // Maneja errores internos
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()]);
    }
}

// Editar categoría
public function update($id)
{
    try {
        // Obtén los datos enviados por la solicitud POST
        $requestData = $this->request->getPost();
        $file = $this->request->getFile('imagen');

        // Depuración: Verificar los datos recibidos
        if (empty($requestData)) {
            return $this->response->setJSON([
                'error' => 'No se han recibido datos.',
                'requestData' => $requestData,
                'file' => $file ? $file->getName() : 'No file received'
            ]);
        }

        // Depuración: Verificar contenido específico
        if (!isset($requestData['nombre']) || empty($requestData['nombre'])) {
            return $this->response->setJSON([
                'error' => 'El campo nombre es obligatorio.',
                'requestData' => $requestData,
                'file' => $file ? $file->getName() : 'No file received'
            ]);
        }

        // Valida los datos utilizando las reglas de validación definidas en el modelo
        $categoriaModel = new CategoriaModel();
        if (!$categoriaModel->validate($requestData)) {
            // Hubo errores de validación
            return $this->response->setJSON(['error' => $categoriaModel->errors()]);
        }

        // Verificar si la categoría existe
        $categoria = $categoriaModel->find($id);
        if (!$categoria) {
            return $this->response->setJSON(['error' => 'Categoría no encontrada.']);
        }

        // Preparar los datos para la actualización
        $categoriaData = [
            'nombre' => $requestData['nombre'],
        ];

        // Capturar la imagen de la categoría y guardarla en el directorio de imágenes
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $extension = 'png';  // Cambiar la extensión de la imagen si es necesario
            $newName = date('Ymd') . '-CT' . mt_rand(1000, 9999) . '.' . $extension;
            $file->move(ROOTPATH . 'public/imagenes/categorias', $newName);

            // Agregar el nuevo nombre de la imagen a los datos de la categoría
            $categoriaData['imagen'] = 'categorias/' . $newName;
        }

        // Actualizar los datos en la base de datos
        if ($categoriaModel->update($id, $categoriaData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Categoría actualizada con éxito']);
        } else {
            return $this->response->setJSON(['error' => 'No se pudo actualizar la categoría.']);
        }
    } catch (\Exception $e) {
        // Maneja errores internos
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()]);
    }
}




public function delete($id)
{
    try {
        $categoriaModel = new CategoriaModel();

        // Verificar si la categoría existe
        $categoria = $categoriaModel->find($id);
        if (!$categoria) {
            return $this->response->setJSON(['error' => 'Categoría no encontrada.']);
        }

        // Cambiar el estado de la categoría a inactivo
        $categoriaData = ['estado' => 0];
        $categoriaModel->update($id, $categoriaData);

        // Comprobar si la actualización fue exitosa
        if ($categoriaModel->affectedRows() > 0) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Categoría desactivada con éxito']);
        } else {
            return $this->response->setJSON(['error' => 'No se pudo desactivar la categoría.']);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON(['error' => 'Error interno: ' . $e->getMessage()]);
    }
}

}
