document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  let lucide // Declare lucide variable
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Cargar la lista de canciones
  cargarCanciones()

  // Función para cargar canciones
  function cargarCanciones() {
    fetch("api/admin/canciones.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          mostrarCanciones(data.canciones)
        } else {
          console.error("Error al cargar canciones:", data.message)
          const tbody = document.querySelector("#tabla-canciones tbody")
          tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error al cargar canciones: ${data.message}</td></tr>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        const tbody = document.querySelector("#tabla-canciones tbody")
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error de conexión</td></tr>`
      })
  }

  // Función para mostrar canciones en la tabla
  function mostrarCanciones(canciones) {
    const tablaCanciones = document.getElementById("tabla-canciones")
    const tbody = tablaCanciones.querySelector("tbody")

    // Limpiar tabla
    tbody.innerHTML = ""

    if (canciones.length === 0) {
      const tr = document.createElement("tr")
      tr.innerHTML = `<td colspan="6" class="text-center">No hay canciones disponibles</td>`
      tbody.appendChild(tr)
      return
    }

    // Agregar canciones a la tabla
    canciones.forEach((cancion) => {
      const tr = document.createElement("tr")

      // Formatear fecha
      const fecha = new Date(cancion.fecha_creacion)
      const fechaFormateada = fecha.toLocaleDateString()

      tr.innerHTML = `
      <td>${cancion.titulo}</td>
      <td>${cancion.artista}</td>
      <td>${cancion.nivel_nombre}</td>
      <td>${cancion.autor}</td>
      <td>${fechaFormateada}</td>
      <td class="table-actions">
        <a href="cancion-form.html?id=${cancion.id}" class="action-btn action-btn-edit">
          <i data-lucide="edit"></i> Editar
        </a>
        <button class="action-btn action-btn-delete" data-id="${cancion.id}">
          <i data-lucide="trash-2"></i> Eliminar
        </button>
        <a href="../cancion.html?id=${cancion.id}" class="action-btn action-btn-view" target="_blank">
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
      btn.addEventListener("click", () => eliminarCancion(btn.dataset.id))
    })
  }

  // Función para eliminar una canción
  function eliminarCancion(id) {
    if (confirm("¿Estás seguro de que deseas eliminar esta canción? Esta acción no se puede deshacer.")) {
      fetch("api/admin/canciones.php", {
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
            alert("Canción eliminada correctamente")
            cargarCanciones() // Recargar la tabla
          } else {
            alert(`Error al eliminar la canción: ${data.message}`)
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")
        })
    }
  }
})

