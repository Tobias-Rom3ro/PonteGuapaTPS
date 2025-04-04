<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%
    // Redirigir al servlet de login
    response.sendRedirect(request.getContextPath() + "/login");
%>