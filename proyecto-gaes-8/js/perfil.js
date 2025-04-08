document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof window.lucide !== "undefined") {
    window.lucide.createIcons()
  }

  // Cargar datos del perfil desde el servidor
  fetch("api/perfil.php", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Actualizar la interfaz con los datos del usuario
        document.getElementById("profile-name").textContent = data.usuario.nombre
        document.getElementById("profile-email").textContent = data.usuario.email
        document.getElementById("profile-role").textContent = "Rol: " + data.usuario.rol

        // Formatear fecha de registro
        const fechaRegistro = new Date(data.usuario.fecha_registro)
        const opciones = { year: "numeric", month: "long", day: "numeric" }
        document.getElementById("join-date").textContent = fechaRegistro.toLocaleDateString("es-ES", opciones)

        // Mostrar avatar si existe
        if (data.usuario.avatar) {
          const avatarImg = document.querySelector(".avatar-image")
          if (avatarImg) {
            avatarImg.src = data.usuario.avatar
          }
        }

        // Actualizar biografía si existe
        if (data.usuario.biografia) {
          const bioElement = document.getElementById("profile-bio")
          if (bioElement) {
            bioElement.textContent = data.usuario.biografia
          }
        }
      } else {
        // Error al cargar perfil, usar datos de sessionStorage como respaldo
        const nombre = sessionStorage.getItem("usuario_nombre") || "Usuario"
        const email = sessionStorage.getItem("usuario_email") || "usuario@example.com"
        const rol = sessionStorage.getItem("usuario_rol") || "Aprendiz"

        document.getElementById("profile-name").textContent = nombre
        document.getElementById("profile-email").textContent = email
        document.getElementById("profile-role").textContent = "Rol: " + rol
      }
    })
    .catch((error) => {
      console.error("Error al cargar perfil:", error)

      // En caso de error, usar datos de sessionStorage
      const nombre = sessionStorage.getItem("usuario_nombre") || "Usuario"
      const email = sessionStorage.getItem("usuario_email") || "usuario@example.com"
      const rol = sessionStorage.getItem("usuario_rol") || "Aprendiz"

      document.getElementById("profile-name").textContent = nombre
      document.getElementById("profile-email").textContent = email
      document.getElementById("profile-role").textContent = "Rol: " + rol
    })

  // Cargar estadísticas de progreso del usuario
  // Esta funcionalidad se implementaría en una fase posterior
})

