package com.ponteguapa.util;

import org.mindrot.jbcrypt.BCrypt;

/**
 * Utilidad para el manejo seguro de contraseñas
 */
public class PasswordUtil {

    /**
     * Verifica si la contraseña proporcionada coincide con el hash almacenado
     * @param plainPassword Contraseña en texto plano a verificar
     * @param hashedPassword Hash almacenado para comparar
     * @return true si la contraseña coincide, false en caso contrario
     */
    public static boolean checkPassword(String plainPassword, String hashedPassword) {
        return BCrypt.checkpw(plainPassword, hashedPassword);
    }

    /**
     * Genera un hash seguro para una contraseña
     * @param plainPassword Contraseña en texto plano a hashear
     * @return Hash de la contraseña
     */
    public static String hashPassword(String plainPassword) {
        return BCrypt.hashpw(plainPassword, BCrypt.gensalt());
    }
}