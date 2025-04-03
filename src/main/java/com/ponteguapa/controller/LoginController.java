package com.ponteguapa.controller;

import com.ponteguapa.model.User;
import com.ponteguapa.model.UserDAO;
import com.ponteguapa.util.PasswordUtil;

/**
 * Controlador para la funcionalidad de login
 */
public class LoginController {
    private UserDAO userDAO;

    public LoginController() {
        this.userDAO = new UserDAO();

        // Inicializamos la base de datos si es necesario
        this.userDAO.initDatabase();
    }

    /**
     * Autentica a un usuario con sus credenciales
     * @param usernameOrEmail Nombre de usuario o email
     * @param password Contraseña en texto plano
     * @return El usuario autenticado o null si la autenticación falla
     */
    public User authenticate(String usernameOrEmail, String password) {
        // Buscar usuario por nombre de usuario o email
        User user = userDAO.findByUsernameOrEmail(usernameOrEmail);

        // Si no se encontró el usuario, autenticación fallida
        if (user == null) {
            return null;
        }

        // Verificar la contraseña
        boolean passwordMatches = PasswordUtil.checkPassword(password, user.getPassword());

        // Si la contraseña no coincide, autenticación fallida
        if (!passwordMatches) {
            return null;
        }

        return user;
    }

    /**
     * Verifica si un usuario es administrador
     * @param userId ID del usuario a verificar
     * @return true si el usuario es administrador, false en caso contrario
     */
    public boolean isAdmin(int userId) {
        return userDAO.isAdmin(userId);
    }
}