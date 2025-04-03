package com.ponteguapa.config;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 * Clase para manejar la conexión a la base de datos PostgreSQL
 */
public class DBConnection {
    // Configuración de la conexión a PostgreSQL
    private static final String URL = "jdbc:postgresql://localhost:5433/ponteguapa";
    private static final String USER = "postgres";
    private static final String PASSWORD = "1234";

    /**
     * Establece y devuelve una conexión a la base de datos
     * @return Un objeto Connection para interactuar con la BD
     * @throws SQLException si no se puede establecer la conexión
     */
    public static Connection getConnection() throws SQLException {
        try {
            // Cargar el driver de PostgreSQL
            Class.forName("org.postgresql.Driver");

            // Devolver la conexión
            return DriverManager.getConnection(URL, USER, PASSWORD);
        } catch (ClassNotFoundException e) {
            throw new SQLException("No se pudo cargar el driver de PostgreSQL", e);
        } catch (SQLException e) {
            throw new SQLException("Error al conectar a la base de datos", e);
        }
    }

    /**
     * Cierra la conexión a la base de datos
     * @param conn Conexión a cerrar
     */
    public static void closeConnection(Connection conn) {
        if (conn != null) {
            try {
                conn.close();
            } catch (SQLException e) {
                System.err.println("Error al cerrar la conexión: " + e.getMessage());
            }
        }
    }
}