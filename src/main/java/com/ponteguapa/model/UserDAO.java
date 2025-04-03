package com.ponteguapa.model;

import com.ponteguapa.config.DBConnection;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

/**
 * Clase de acceso a datos (DAO) para operaciones de usuario en la base de datos
 */
public class UserDAO {

    /**
     * Busca un usuario por su nombre de usuario o email
     * @param usernameOrEmail El nombre de usuario o email para buscar
     * @return El usuario encontrado o null si no existe
     */
    public User findByUsernameOrEmail(String usernameOrEmail) {
        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;
        User user = null;

        try {
            conn = DBConnection.getConnection();
            String sql = "SELECT * FROM users WHERE username = ? OR email = ?";
            stmt = conn.prepareStatement(sql);
            stmt.setString(1, usernameOrEmail);
            stmt.setString(2, usernameOrEmail);

            rs = stmt.executeQuery();

            if (rs.next()) {
                user = new User();
                user.setId(rs.getInt("id"));
                user.setUsername(rs.getString("username"));
                user.setPassword(rs.getString("password"));
                user.setRole(rs.getString("role"));
                user.setEmail(rs.getString("email"));
            }

        } catch (SQLException e) {
            System.err.println("Error al buscar usuario: " + e.getMessage());
        } finally {
            try {
                if (rs != null) rs.close();
                if (stmt != null) stmt.close();
                DBConnection.closeConnection(conn);
            } catch (SQLException e) {
                System.err.println("Error al cerrar recursos: " + e.getMessage());
            }
        }

        return user;
    }

    /**
     * Verifica si el usuario es administrador
     * @param userId ID del usuario a verificar
     * @return true si el usuario es administrador, false en caso contrario
     */
    public boolean isAdmin(int userId) {
        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;
        boolean isAdmin = false;

        try {
            conn = DBConnection.getConnection();
            String sql = "SELECT role FROM users WHERE id = ?";
            stmt = conn.prepareStatement(sql);
            stmt.setInt(1, userId);

            rs = stmt.executeQuery();

            if (rs.next()) {
                String role = rs.getString("role");
                isAdmin = "admin".equalsIgnoreCase(role);
            }

        } catch (SQLException e) {
            System.err.println("Error al verificar rol de administrador: " + e.getMessage());
        } finally {
            try {
                if (rs != null) rs.close();
                if (stmt != null) stmt.close();
                DBConnection.closeConnection(conn);
            } catch (SQLException e) {
                System.err.println("Error al cerrar recursos: " + e.getMessage());
            }
        }

        return isAdmin;
    }

    /**
     * Crea el schema inicial de la base de datos si no existe
     */
    public void initDatabase() {
        Connection conn = null;
        PreparedStatement stmt = null;

        try {
            conn = DBConnection.getConnection();

            // Crear tabla de usuarios si no existe
            String createTableSQL =
                    "CREATE TABLE IF NOT EXISTS users (" +
                            "id SERIAL PRIMARY KEY, " +
                            "username VARCHAR(50) UNIQUE NOT NULL, " +
                            "password VARCHAR(255) NOT NULL, " +
                            "email VARCHAR(100) UNIQUE NOT NULL, " +
                            "role VARCHAR(20) NOT NULL DEFAULT 'user', " +
                            "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP" +
                            ")";

            stmt = conn.prepareStatement(createTableSQL);
            stmt.execute();

            // Verificar si existe el usuario admin
            stmt = conn.prepareStatement("SELECT COUNT(*) FROM users WHERE role = 'admin'");
            ResultSet rs = stmt.executeQuery();
            rs.next();
            int adminCount = rs.getInt(1);

            // Si no hay admin, crear uno por defecto
            if (adminCount == 0) {
                String hashedPassword = org.mindrot.jbcrypt.BCrypt.hashpw("admin123", org.mindrot.jbcrypt.BCrypt.gensalt());

                String insertAdminSQL =
                        "INSERT INTO users (username, password, email, role) " +
                                "VALUES (?, ?, ?, 'admin')";

                stmt = conn.prepareStatement(insertAdminSQL);
                stmt.setString(1, "admin");
                stmt.setString(2, hashedPassword);
                stmt.setString(3, "admin@ponteguapa.com");
                stmt.executeUpdate();

                System.out.println("Usuario administrador creado con éxito.");
            }

        } catch (SQLException e) {
            System.err.println("Error al inicializar la base de datos: " + e.getMessage());
        } finally {
            try {
                if (stmt != null) stmt.close();
                DBConnection.closeConnection(conn);
            } catch (SQLException e) {
                System.err.println("Error al cerrar recursos: " + e.getMessage());
            }
        }
    }
}