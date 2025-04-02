

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class Usuario {
    private Connection conexion;

    public Usuario() {
        this.conexion = Conexion.getConexion();
    }

    public boolean registrar(String nombre, String correo, String contrasena) {
        String sql = "INSERT INTO usuario (nombre_usuario, correo_electronico, contrasena) VALUES (?, ?, ?)";

        try (PreparedStatement stmt = conexion.prepareStatement(sql)) {
            stmt.setString(1, nombre);
            stmt.setString(2, correo);
            stmt.setString(3, contrasena);
            return stmt.executeUpdate() > 0;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean verificarCredenciales(String correo, String contrasena) {
        String sql = "SELECT contrasena FROM usuario WHERE correo_electronico = ?";

        try (PreparedStatement stmt = conexion.prepareStatement(sql)) {
            stmt.setString(1, correo);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                String hashedPassword = rs.getString("contrasena");
                return contrasena.equals(hashedPassword);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return false;
    }
}
