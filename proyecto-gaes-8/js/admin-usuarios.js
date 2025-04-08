document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Cargar la lista de usuarios
  cargarUsuarios()

  // Función para cargar usuarios
  function cargarUsuarios() {
    fetch("api/admin/usuarios.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          mostrarUsuarios(data.usuarios)
        } else {
          console.error("Error al cargar usuarios:", data.message)
          const tbody = document.querySelector("#tabla-usuarios tbody")
          tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error al cargar usuarios: ${data.message}</td></tr>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        const tbody = document.querySelector("#tabla-usuarios tbody")
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error de conexión</td></tr>`
      })
  }

  // Función para mostrar usuarios en la tabla
  function mostrarUsuarios(usuarios) {
    const tablaUsuarios = document.getElementById("tabla-usuarios")
    const tbody = tablaUsuarios.querySelector("tbody")

    // Limpiar tabla
    tbody.innerHTML = ""

    if (usuarios.length === 0) {
      const tr = document.createElement("tr")
      tr.innerHTML = `<td colspan="6" class="text-center">No hay usuarios registrados</td>`
      tbody.appendChild(tr)
      return
    }

    // Obtener el ID del usuario actual
    const usuarioActualId = sessionStorage.getItem("usuario_id") || ""

    // Agregar usuarios a la tabla
    usuarios.forEach((usuario) => {
      const tr = document.createElement("tr")

      // Formatear fechas
      const fechaRegistro = new Date(usuario.fecha_registro)
      const fechaRegistroFormateada = fechaRegistro.toLocaleDateString()

      let ultimoAccesoFormateado = "Nunca"
      if (usuario.ultimo_acceso) {
        const ultimoAcceso = new Date(usuario.ultimo_acceso)
        ultimoAccesoFormateado = ultimoAcceso.toLocaleDateString() + " " + ultimoAcceso.toLocaleTimeString()
      }

      // Determinar si es el usuario actual
      const esUsuarioActual = usuario.id == usuarioActualId

      tr.innerHTML = `
      <td>${usuario.nombre}${esUsuarioActual ? " (Tú)" : ""}</td>
      <td>${usuario.email}</td>
      <td>${usuario.rol}</td>
      <td>${fechaRegistroFormateada}</td>
      <td>${ultimoAccesoFormateado}</td>
      <td class="table-actions">
        <a href="usuario-form.html?id=${usuario.id}" class="action-btn action-btn-edit">
          <i data-lucide="edit"></i> Editar
        </a>
        ${
          !esUsuarioActual
            ? `
          <button class="action-btn action-btn-delete" data-id="${usuario.id}">
            <i data-lucide="trash-2"></i> Eliminar
          </button>
        `
            : ""
        }
        <a href="../perfil.html?id=${usuario.id}" class="action-btn action-btn-view" target="_blank">
          <i data-lucide="eye"></i> Ver
        </a>
      </td>
    `
      tbody.appendChild(tr)
    })

    // Inicializar iconos en los nuevos botones
    if (typeof lucide !== "undefined") {
      lucide.createIcons()
    }

    // Agregar eventos a los botones de eliminar
    document.querySelectorAll(".action-btn-delete").forEach((btn) => {
      btn.addEventListener("click", () => eliminarUsuario(btn.dataset.id))
    })
  }

  // Función para eliminar un usuario
  function eliminarUsuario(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.")) {
      fetch("api/admin/usuarios.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id: id,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Usuario eliminado correctamente")
            cargarUsuarios() // Recargar la tabla
          } else {
            alert(`Error al eliminar el usuario: ${data.message}`)
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")
        })
    }
  }
})

