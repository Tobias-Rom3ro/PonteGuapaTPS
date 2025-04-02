package controlador;

import modelo.Usuario;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;
import java.io.IOException;

@WebServlet("/ControladorUsuario") // URL del servlet
public class ControladorUsuario extends HttpServlet {
    private static final long serialVersionUID = 1L;
    private Usuario modelo;

    public ControladorUsuario() {
        super();
        modelo = new Usuario();
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        String action = request.getParameter("action");

        if (action == null) {
            mostrarLogin(request, response);
        } else {
            switch (action) {
                case "mostrarRegistro":
                    mostrarRegistro(request, response);
                    break;
                default:
                    mostrarLogin(request, response);
                    break;
            }
        }
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        String action = request.getParameter("action");

        if (action == null) {
            mostrarLogin(request, response);
        } else {
            switch (action) {
                case "iniciarSesion":
                    iniciarSesion(request, response);
                    break;
                case "registrar":
                    registrarUsuario(request, response);
                    break;
                default:
                    mostrarLogin(request, response);
                    break;
            }
        }
    }

    private void mostrarLogin(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        request.getRequestDispatcher("vista/login.html").forward(request, response);
    }

    private void iniciarSesion(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        String correo = request.getParameter("correo_electronico");
        String contrasena = request.getParameter("contrasena");

        if (modelo.verificarCredenciales(correo, contrasena)) {
            HttpSession session = request.getSession();
            session.setAttribute("usuario", correo);
            response.sendRedirect("vista/bienvenida.html");
        } else {
            request.setAttribute("error", "Credenciales incorrectas.");
            mostrarLogin(request, response);
        }
    }

    private void mostrarRegistro(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        request.getRequestDispatcher("vista/registro.html").forward(request, response);
    }

    private void registrarUsuario(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        String nombre = request.getParameter("nombre_usuario");
        String correo = request.getParameter("correo_electronico");
        String contrasena = request.getParameter("contrasena");

        if (modelo.registrar(nombre, correo, contrasena)) {
            request.setAttribute("mensaje", "Registro exitoso. Ahora puedes iniciar sesión.");
            mostrarLogin(request, response);
        } else {
            request.setAttribute("error", "Error al registrar.");
            mostrarRegistro(request, response);
        }
    }
}
