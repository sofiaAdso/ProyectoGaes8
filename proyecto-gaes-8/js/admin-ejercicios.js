document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Cargar la lista de ejercicios
  cargarEjercicios()

  // Función para cargar ejercicios
  function cargarEjercicios() {
    fetch("api/admin/ejercicios.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          mostrarEjercicios(data.ejercicios)
        } else {
          console.error("Error al cargar ejercicios:", data.message)
          const tbody = document.querySelector("#tabla-ejercicios tbody")
          tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error al cargar ejercicios: ${data.message}</td></tr>`
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        const tbody = document.querySelector("#tabla-ejercicios tbody")
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error de conexión</td></tr>`
      })
  }

  // Función para mostrar ejercicios en la tabla
  function mostrarEjercicios(ejercicios) {
    const tablaEjercicios = document.getElementById("tabla-ejercicios")
    const tbody = tablaEjercicios.querySelector("tbody")

    // Limpiar tabla
    tbody.innerHTML = ""

    if (ejercicios.length === 0) {
      const tr = document.createElement("tr")
      tr.innerHTML = `<td colspan="6" class="text-center">No hay ejercicios disponibles</td>`
      tbody.appendChild(tr)
      return
    }

    // Agregar ejercicios a la tabla
    ejercicios.forEach((ejercicio) => {
      const tr = document.createElement("tr")

      // Formatear fecha
      const fecha = new Date(ejercicio.fecha_creacion)
      const fechaFormateada = fecha.toLocaleDateString()

      tr.innerHTML = `
      <td>${ejercicio.titulo}</td>
      <td>${ejercicio.tipo}</td>
      <td>${ejercicio.nivel_nombre}</td>
      <td>${ejercicio.autor}</td>
      <td>${fechaFormateada}</td>
      <td class="table-actions">
        <a href="ejercicio-form.html?id=${ejercicio.id}" class="action-btn action-btn-edit">
          <i data-lucide="edit"></i> Editar
        </a>
        <button class="action-btn action-btn-delete" data-id="${ejercicio.id}">
          <i data-lucide="trash-2"></i> Eliminar
        </button>
        <a href="../ejercicio.html?id=${ejercicio.id}" class="action-btn action-btn-view" target="_blank">
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
      btn.addEventListener("click", () => eliminarEjercicio(btn.dataset.id))
    })
  }

  // Función para eliminar un ejercicio
  function eliminarEjercicio(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este ejercicio? Esta acción no se puede deshacer.")) {
      fetch("api/admin/ejercicios.php", {
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
            alert("Ejercicio eliminado correctamente")
            cargarEjercicios() // Recargar la tabla
          } else {
            alert(`Error al eliminar el ejercicio: ${data.message}`)
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")
        })
    }
  }
})

