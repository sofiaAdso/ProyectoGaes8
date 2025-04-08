document.addEventListener("DOMContentLoaded", () => {
  // Limpiar sessionStorage al cargar la página de inicio después de cerrar sesión
  if (window.location.search.includes("logout=success")) {
    // Limpiar datos de sesión del almacenamiento local
    sessionStorage.removeItem("usuario_id")
    sessionStorage.removeItem("usuario_nombre")
    sessionStorage.removeItem("usuario_email")
    sessionStorage.removeItem("usuario_rol")

    // Mostrar mensaje de cierre de sesión exitoso
    alert("Has cerrado sesión correctamente.")

    // Redirigir a la página principal sin parámetros
    window.history.replaceState({}, document.title, window.location.pathname)
  }

  // Agregar evento de cierre de sesión a los enlaces de logout
  const logoutLinks = document.querySelectorAll('a[href="logout.php"]')
  logoutLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      // No prevenir el comportamiento por defecto, permitir que el enlace funcione normalmente
      // para que se ejecute el script PHP de logout
    })
  })
})

