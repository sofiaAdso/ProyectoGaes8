document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Cargar la lista de lecciones
  cargarLecciones()

  // Función para cargar lecciones
  function cargarLecciones() {
    fetch("api/admin/lecciones.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          mostrarLecciones(data.lecciones)
        } else {
          console.error("Error al cargar lecciones:", data.message)
          const tbody = document.querySelector("#tabla-lecciones tbody")
          tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error al cargar lecciones: ${data.message}</td></tr>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        const tbody = document.querySelector("#tabla-lecciones tbody")
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error de conexión</td></tr>`
      })
  }

  // Función para mostrar lecciones en la tabla
  function mostrarLecciones(lecciones) {
    const tablaLecciones = document.getElementById("tabla-lecciones")
    const tbody = tablaLecciones.querySelector("tbody")

    // Limpiar tabla
    tbody.innerHTML = ""

    if (lecciones.length === 0) {
      const tr = document.createElement("tr")
      tr.innerHTML = `<td colspan="6" class="text-center">No hay lecciones disponibles</td>`
      tbody.appendChild(tr)
      return
    }

    // Agregar lecciones a la tabla
    lecciones.forEach((leccion) => {
      const tr = document.createElement("tr")

      // Formatear fecha
      const fecha = new Date(leccion.fecha_creacion)
      const fechaFormateada = fecha.toLocaleDateString()

      tr.innerHTML = `
      <td>${leccion.titulo}</td>
      <td>${leccion.categoria_nombre}</td>
      <td>${leccion.nivel_nombre}</td>
      <td>${leccion.autor}</td>
      <td>${fechaFormateada}</td>
      <td class="table-actions">
        <a href="leccion-form.html?id=${leccion.id}" class="action-btn action-btn-edit">
          <i data-lucide="edit"></i> Editar
        </a>
        <button class="action-btn action-btn-delete" data-id="${leccion.id}">
          <i data-lucide="trash-2"></i> Eliminar
        </button>
        <a href="../leccion.html?id=${leccion.id}" class="action-btn action-btn-view" target="_blank">
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
      btn.addEventListener("click", () => eliminarLeccion(btn.dataset.id))
    })
  }

  // Función para eliminar una lección
  function eliminarLeccion(id) {
    if (confirm("¿Estás seguro de que deseas eliminar esta lección? Esta acción no se puede deshacer.")) {
      fetch("api/admin/lecciones.php", {
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
            alert("Lección eliminada correctamente")
            cargarLecciones() // Recargar la tabla
          } else {
            alert(`Error al eliminar la lección: ${data.message}`)
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")
        })
    }
  }
})

