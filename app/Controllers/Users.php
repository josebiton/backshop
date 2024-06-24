<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Users extends BaseController
{
    public function index()
{
    try {
        $userModel = new UserModel();
        $users = $userModel->where('estado', 'A')->findAll();
        return $this->response->setJSON($users);
    } catch (\Exception $e) {
        return $this->response->setJSON(['error' => $e->getMessage()]);
    }
}

public function show($id)
{
    try {
        $userModel = new UserModel();
        $user = $userModel->where('estado', 'A')->find($id);
        if (!$user) {
            return $this->response->setJSON(['error' => 'Usuario no encontrado o inactivo.']);
        }
        return $this->response->setJSON($user);
    } catch (\Exception $e) {
        return $this->response->setJSON(['error' => $e->getMessage()]);
    }
}

    // editar un usuario
    public function update($id)
    {
        try {
            $inputData = $this->request->getPost();
            $usuarioModel = new UserModel();

            // Verificar si el usuario existe
            $usuario = $usuarioModel->find($id);
            if (!$usuario) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado.']);
            }

            // Validar si ya existe otro usuario con el mismo correo o teléfono
            if ($usuarioModel->isDuplicate($inputData)) {
                return $this->response->setJSON(['error' => 'Ya existe otro usuario con el mismo correo o teléfono.']);
            }

            // Iniciar transacción
            $usuarioModel->transStart();

            // Validación de los campos
            if (!$usuarioModel->validate($inputData)) {
                $usuarioModel->transRollback(); // Revertir transacción en caso de fallo en la validación
                return $this->response->setJSON(['error' => $usuarioModel->errors()]);
            }

            $email = $inputData['email'];
            // Verificar si es una dirección de correo electrónico válida
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON(['error' => 'La dirección de correo electrónico no es válida.']);
            }

            // Verificar si es un dominio permitido (puedes ajustar la lista según tus necesidades)
            $allowedDomains = ['gmail.com', 'hotmail.com', 'upeu.edu.pe', 'outlook.com'];
            $domain = strtolower(substr(strrchr($email, "@"), 1));

            if (!in_array($domain, $allowedDomains)) {
                return $this->response->setJSON(['error' => 'No se permiten direcciones de correo electrónico de este dominio.']);
            }

            // Preparar los datos para la actualización
            $usuarioData = [
                'name' => strtoupper($inputData['name']),
                'email' => $email,
                'telefono' => $inputData['telefono'],
            ];

            // Si se proporciona una nueva contraseña, actualizarla también
            if (!empty($inputData['password'])) {
                $usuarioData['password'] = hashPassword($inputData['password']);
            }

            // Actualizar el registro de usuario
            $usuarioModel->update($id, $usuarioData);

            // Comprobar si la actualización fue exitosa
            if ($usuarioModel->affectedRows() > 0) {
                $usuarioModel->transComplete(); // Confirmar transacción
                return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario actualizado con éxito']);
            } else {
                $usuarioModel->transRollback(); // Revertir transacción en caso de fallo
                return $this->response->setJSON(['error' => 'No se pudo actualizar el usuario.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // Eliminar usuario
    public function delete($id)
    {
        try {
            $usuarioModel = new UserModel();
    
            // Verificar si el usuario existe
            $usuario = $usuarioModel->find($id);
            if (!$usuario) {
                return $this->response->setJSON(['error' => 'Usuario no encontrado.']);
            }
    
            // Cambiar el estado del usuario a "I" (inactivo)
            $usuarioData = ['estado' => 'I'];
            $usuarioModel->update($id, $usuarioData);
    
            // Comprobar si la actualización fue exitosa
            if ($usuarioModel->affectedRows() > 0) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario desactivado con éxito']);
            } else {
                return $this->response->setJSON(['error' => 'No se pudo desactivar el usuario.']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error interno: ' . $e->getMessage()]);
        }
    }
    

}
