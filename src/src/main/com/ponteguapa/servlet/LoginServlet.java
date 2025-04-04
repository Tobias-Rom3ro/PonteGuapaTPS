package com.ponteguapa.servlet;

import com.ponteguapa.controller.LoginController;
import com.ponteguapa.model.User;
import org.json.JSONObject;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;
import java.io.PrintWriter;

/**
 * Servlet que maneja las solicitudes de login
 */
@WebServlet("/login")
public class LoginServlet extends HttpServlet {
    private LoginController loginController;

    @Override
    public void init() throws ServletException {
        this.loginController = new LoginController();
    }

    /**
     * Maneja las solicitudes GET para mostrar la página de login
     */
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        // Verificar si el usuario ya está logueado
        HttpSession session = request.getSession(false);
        if (session != null && session.getAttribute("user") != null) {
            // Usuario ya logueado, redirigir al dashboard
            response.sendRedirect("pages/dashboard.html");
            return;
        }

        // Usuario no logueado, mostrar página de login
        response.sendRedirect(request.getContextPath() + "/pages/login.html");

    }

    /**
     * Maneja las solicitudes POST para procesar el login
     */
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        // Obtener credenciales del formulario de login
        String usernameOrEmail = request.getParameter("email");
        String password = request.getParameter("password");

        // Configurar la respuesta como JSON
        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();
        JSONObject jsonResponse = new JSONObject();

        // Validar campos
        if (usernameOrEmail == null || usernameOrEmail.trim().isEmpty() ||
                password == null || password.trim().isEmpty()) {

            jsonResponse.put("success", false);
            jsonResponse.put("message", "Usuario/email y contraseña son requeridos");
            out.print(jsonResponse.toString());
            return;
        }

        // Autenticar al usuario
        User user = loginController.authenticate(usernameOrEmail, password);

        if (user == null) {
            // Autenticación fallida
            jsonResponse.put("success", false);
            jsonResponse.put("message", "Credenciales inválidas");
            out.print(jsonResponse.toString());
            return;
        }

        // Verificar si es admin (solo permitimos login de admin por ahora)
        if (!"admin".equalsIgnoreCase(user.getRole())) {
            jsonResponse.put("success", false);
            jsonResponse.put("message", "Acceso permitido solo para administradores");
            out.print(jsonResponse.toString());
            return;
        }

        // Autenticación exitosa, crear sesión
        HttpSession session = request.getSession(true);
        session.setAttribute("user", user);
        session.setAttribute("userId", user.getId());
        session.setAttribute("username", user.getUsername());
        session.setAttribute("role", user.getRole());

        // Enviar respuesta exitosa
        jsonResponse.put("success", true);
        jsonResponse.put("message", "Login exitoso");
        jsonResponse.put("redirectUrl", "pages/dashboard.html");
        out.print(jsonResponse.toString());
    }
}