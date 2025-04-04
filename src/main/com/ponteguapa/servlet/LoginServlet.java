package com.ponteguapa.servlet;

import com.ponteguapa.controller.LoginController;
import com.ponteguapa.model.User;
import org.json.JSONObject;

import javax.servlet.ServletException;
import javax.servlet.http.*;
import java.io.IOException;
import java.io.PrintWriter;

public class LoginServlet extends HttpServlet {
    private LoginController loginController;

    @Override
    public void init() throws ServletException {
        this.loginController = new LoginController();
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        HttpSession session = request.getSession(false);
        if (session != null && session.getAttribute("user") != null) {
            response.sendRedirect("pages/dashboard.html");
            return;
        }

        response.sendRedirect(request.getContextPath() + "/pages/login.html");
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        // Configurar la respuesta como JSON
        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        PrintWriter out = response.getWriter();
        JSONObject jsonResponse = new JSONObject();

        try {
            // Obtener parámetros enviados desde el formulario
            String usernameOrEmail = request.getParameter("email");
            String password = request.getParameter("password");

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
                jsonResponse.put("success", false);
                jsonResponse.put("message", "Credenciales inválidas");
                out.print(jsonResponse.toString());
                return;
            }

            // Verificar si es admin
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

            jsonResponse.put("success", true);
            jsonResponse.put("message", "Login exitoso");
            jsonResponse.put("redirectUrl", "pages/dashboard.html");
        } catch (Exception e) {
            e.printStackTrace();
            jsonResponse.put("success", false);
            jsonResponse.put("message", "Error interno del servidor: " + e.getMessage());
        }

        out.print(jsonResponse.toString());
    }

}
