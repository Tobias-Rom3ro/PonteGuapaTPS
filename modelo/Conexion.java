
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Conexion {
    private static final String URL = "jdbc:postgresql://localhost:5432/peluqueria";
    private static final String USER = "tu_usuario";
    private static final String PASSWORD = "tu_contraseña";
    private static Connection conexion = null;

    public static Connection getConexion() {
        if (conexion == null) {
            try {
                Class.forName("org.postgresql.Driver");
                conexion = DriverManager.getConnection(URL, USER, PASSWORD);
            } catch (ClassNotFoundException | SQLException e) {
                e.printStackTrace();
            }
        }
        return conexion;
    }
}
