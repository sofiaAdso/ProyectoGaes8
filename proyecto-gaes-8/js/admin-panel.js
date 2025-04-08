document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Cargar nombre del administrador
  if (typeof sessionStorage !== "undefined" && sessionStorage.getItem("usuario_nombre")) {
    document.getElementById("admin-name").textContent = sessionStorage.getItem("usuario_nombre")
  } else {
    // Verificar sesión con el servidor
    fetch("api/auth.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.getElementById("admin-name").textContent = data.usuario.nombre
        } else {
          document.getElementById("admin-name").textContent = "Administrador"
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        document.getElementById("admin-name").textContent = "Administrador"
      })
  }

  // Cargar estadísticas
  cargarEstadisticas()

  // Cargar últimos usuarios
  cargarUltimosUsuarios()

  // Cargar últimos mensajes
  cargarUltimosMensajes()

  // Función para cargar estadísticas
  function cargarEstadisticas() {
    fetch("api/admin/estadisticas.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.getElementById("stat-usuarios").textContent = data.stats.usuarios || 0
          document.getElementById("stat-lecciones").textContent = data.stats.lecciones || 0
          document.getElementById("stat-ejercicios").textContent = data.stats.ejercicios || 0
          document.getElementById("stat-canciones").textContent = data.stats.canciones || 0
        } else {
          console.error("Error al cargar estadísticas:", data.message)
          // Mostrar valores por defecto
          document.getElementById("stat-usuarios").textContent = "0"
          document.getElementById("stat-lecciones").textContent = "0"
          document.getElementById("stat-ejercicios").textContent = "0"
          document.getElementById("stat-canciones").textContent = "0"
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        // Mostrar valores por defecto
        document.getElementById("stat-usuarios").textContent = "0"
        document.getElementById("stat-lecciones").textContent = "0"
        document.getElementById("stat-ejercicios").textContent = "0"
        document.getElementById("stat-canciones").textContent = "0"
      })
  }

  // Función para cargar últimos usuarios
  function cargarUltimosUsuarios() {
    fetch("api/admin/usuarios.php?limit=5", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const tbody = document.getElementById("tabla-usuarios")
          tbody.innerHTML = ""

          if (data.usuarios.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4">No hay usuarios registrados</td></tr>`
            return
          }

          data.usuarios.forEach((usuario) => {
            const fecha = new Date(usuario.fecha_registro)
            const fechaFormateada = fecha.toLocaleDateString()

            tbody.innerHTML += `
          <tr>
            <td>${usuario.nombre}</td>
            <td>${usuario.email}</td>
            <td>${usuario.rol}</td>
            <td>${fechaFormateada}</td>
          </tr>
        `
          })
        } else {
          console.error("Error al cargar usuarios:", data.message)
          document.getElementById("tabla-usuarios").innerHTML = `<tr><td colspan="4">Error al cargar usuarios</td></tr>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        document.getElementById("tabla-usuarios").innerHTML = `<tr><td colspan="4">Error de conexión</td></tr>`
      })
  }

  // Función para cargar últimos mensajes
  function cargarUltimosMensajes() {
    fetch("api/admin/mensajes.php?limit=5", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const tbody = document.getElementById("tabla-mensajes")
          tbody.innerHTML = ""

          if (data.mensajes.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5">No hay mensajes</td></tr>`
            return
          }

          data.mensajes.forEach((mensaje) => {
            const fecha = new Date(mensaje.fecha)
            const fechaFormateada = fecha.toLocaleDateString()
            const leido = mensaje.leido == 1 ? "Sí" : "No"
            const claseLeido = mensaje.leido == 0 ? "mensaje-no-leido" : ""

            tbody.innerHTML += `
          <tr class="${claseLeido}">
            <td>${mensaje.nombre}</td>
            <td>${mensaje.email}</td>
            <td>${mensaje.asunto}</td>
            <td>${fechaFormateada}</td>
            <td>${leido}</td>
          </tr>
        `
          })
        } else {
          console.error("Error al cargar mensajes:", data.message)
          document.getElementById("tabla-mensajes").innerHTML = `<tr><td colspan="5">Error al cargar mensajes</td></tr>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        document.getElementById("tabla-mensajes").innerHTML = `<tr><td colspan="5">Error de conexión</td></tr>`
      })
  }
})

