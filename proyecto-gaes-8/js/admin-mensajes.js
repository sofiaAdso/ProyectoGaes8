document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Cargar la lista de mensajes
  cargarMensajes()

  // Función para cargar mensajes
  function cargarMensajes() {
    fetch("api/admin/mensajes.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          mostrarMensajes(data.mensajes)
        } else {
          console.error("Error al cargar mensajes:", data.message)
          const tbody = document.querySelector("#tabla-mensajes tbody")
          tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error al cargar mensajes: ${data.message}</td></tr>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        const tbody = document.querySelector("#tabla-mensajes tbody")
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error de conexión</td></tr>`
      })
  }

  // Función para mostrar mensajes en la tabla
  function mostrarMensajes(mensajes) {
    const tablaMensajes = document.getElementById("tabla-mensajes")
    const tbody = tablaMensajes.querySelector("tbody")

    // Limpiar tabla
    tbody.innerHTML = ""

    if (mensajes.length === 0) {
      const tr = document.createElement("tr")
      tr.innerHTML = `<td colspan="6" class="text-center">No hay mensajes disponibles</td>`
      tbody.appendChild(tr)
      return
    }

    // Agregar mensajes a la tabla
    mensajes.forEach((mensaje) => {
      const tr = document.createElement("tr")

      // Destacar mensajes no leídos
      if (mensaje.leido == 0) {
        tr.classList.add("mensaje-no-leido")
      }

      // Formatear fecha
      const fecha = new Date(mensaje.fecha)
      const fechaFormateada = fecha.toLocaleDateString() + " " + fecha.toLocaleTimeString()

      tr.innerHTML = `
      <td>${mensaje.nombre}</td>
      <td>${mensaje.email}</td>
      <td>${mensaje.asunto}</td>
      <td>${fechaFormateada}</td>
      <td>${mensaje.leido == 1 ? "Sí" : "No"}</td>
      <td class="table-actions">
        <button class="action-btn action-btn-view" data-id="${mensaje.id}">
          <i data-lucide="eye"></i> Ver
        </button>
        <button class="action-btn action-btn-delete" data-id="${mensaje.id}">
          <i data-lucide="trash-2"></i> Eliminar
        </button>
      </td>
    `
      tbody.appendChild(tr)
    })

    // Inicializar iconos en los nuevos botones
    if (typeof lucide !== "undefined") {
      lucide.createIcons()
    }

    // Agregar eventos a los botones
    document.querySelectorAll(".action-btn-view").forEach((btn) => {
      btn.addEventListener("click", () => verMensaje(btn.dataset.id))
    })

    document.querySelectorAll(".action-btn-delete").forEach((btn) => {
      btn.addEventListener("click", () => eliminarMensaje(btn.dataset.id))
    })
  }

  // Función para ver un mensaje
  function verMensaje(id) {
    fetch(`api/admin/mensajes.php?id=${id}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const mensaje = data.mensaje

          // Formatear fecha
          const fecha = new Date(mensaje.fecha)
          const fechaFormateada = fecha.toLocaleDateString() + " " + fecha.toLocaleTimeString()

          // Mostrar modal con el mensaje
          const modalHTML = `
        <div class="modal-backdrop"></div>
        <div class="modal-container">
          <div class="modal-header">
            <h3>Mensaje de ${mensaje.nombre}</h3>
            <button class="modal-close">&times;</button>
          </div>
          <div class="modal-body">
            <p><strong>De:</strong> ${mensaje.nombre} (${mensaje.email})</p>
            <p><strong>Asunto:</strong> ${mensaje.asunto}</p>
            <p><strong>Fecha:</strong> ${fechaFormateada}</p>
            <hr>
            <div class="mensaje-contenido">
              ${mensaje.mensaje}
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary modal-close">Cerrar</button>
            <a href="mailto:${mensaje.email}" class="btn btn-primary">Responder por Email</a>
          </div>
        </div>
      `

          // Insertar modal en el DOM
          const modalContainer = document.createElement("div")
          modalContainer.className = "modal"
          modalContainer.innerHTML = modalHTML
          document.body.appendChild(modalContainer)

          // Agregar eventos para cerrar el modal
          modalContainer.querySelectorAll(".modal-close").forEach((btn) => {
            btn.addEventListener("click", () => {
              document.body.removeChild(modalContainer)
            })
          })

          // Actualizar la tabla para reflejar que el mensaje fue leído
          cargarMensajes()
        } else {
          alert(`Error al cargar el mensaje: ${data.message}`)
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")
      })
  }

  // Función para eliminar un mensaje
  function eliminarMensaje(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este mensaje? Esta acción no se puede deshacer.")) {
      fetch("api/admin/mensajes.php", {
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
            alert("Mensaje eliminado correctamente")
            cargarMensajes() // Recargar la tabla
          } else {
            alert(`Error al eliminar el mensaje: ${data.message}`)
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")
        })
    }
  }
})

