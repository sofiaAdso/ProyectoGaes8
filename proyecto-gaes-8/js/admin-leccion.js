document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  let lucide // Declare lucide
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Inicializar editor Quill
  let quill // Declare quill
  try {
    quill = new Quill("#editor-contenido", {
      theme: "snow",
      modules: {
        toolbar: [
          [{ header: [1, 2, 3, 4, 5, 6, false] }],
          ["bold", "italic", "underline", "strike"],
          [{ list: "ordered" }, { list: "bullet" }],
          [{ color: [] }, { background: [] }],
          ["link", "image", "video"],
          ["clean"],
        ],
      },
    })
  } catch (error) {
    console.error("Quill initialization error:", error)
    alert("Failed to initialize the Quill editor. Please check your setup.")
    return // Exit if Quill fails to initialize
  }

  // Cargar niveles y categorías
  cargarNiveles()
  cargarCategorias()

  // Verificar si estamos editando una lección existente
  const urlParams = new URLSearchParams(window.location.search)
  const leccionId = urlParams.get("id")

  if (leccionId) {
    // Cambiar el título del formulario
    document.getElementById("form-title").textContent = "Editar Lección"

    // Cargar datos de la lección
    cargarLeccion(leccionId)
  }

  // Validación del formulario de lección
  const leccionForm = document.getElementById("leccion-form")
  if (leccionForm) {
    console.log("Formulario de lección encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(leccionForm)

    leccionForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de lección enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const id = document.getElementById("id").value
      const titulo = document.getElementById("titulo").value.trim()
      const descripcion = document.getElementById("descripcion").value.trim()
      const contenido = quill.root.innerHTML
      document.getElementById("contenido").value = contenido // Actualizar el campo oculto
      const nivel = document.getElementById("nivel").value
      const categoria = document.getElementById("categoria").value

      let hasErrors = false

      // Validar título
      if (!titulo) {
        showError("titulo", "El título es obligatorio")
        hasErrors = true
      }

      // Validar descripción
      if (!descripcion) {
        showError("descripcion", "La descripción es obligatoria")
        hasErrors = true
      }

      // Validar contenido
      if (!contenido || contenido === "<p><br></p>") {
        showError("contenido", "El contenido es obligatorio")
        hasErrors = true
      }

      // Validar nivel
      if (!nivel) {
        showError("nivel", "El nivel es obligatorio")
        hasErrors = true
      }

      // Validar categoría
      if (!categoria) {
        showError("categoria", "La categoría es obligatoria")
        hasErrors = true
      }

      // Si no hay errores, enviar el formulario
      if (!hasErrors) {
        console.log("Enviando datos de lección al servidor")

        // Mostrar indicador de carga
        const submitBtn = leccionForm.querySelector('button[type="submit"]')
        const originalText = submitBtn.innerHTML
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner"></span> Guardando...'

        // Preparar datos para enviar
        const leccionData = {
          titulo: titulo,
          descripcion: descripcion,
          contenido: contenido,
          nivel_id: nivel,
          categoria_id: categoria,
        }

        // Si es edición, agregar ID
        if (id) {
          leccionData.id = id
        }

        // Determinar método y URL
        const method = id ? "PUT" : "POST"

        // Enviar datos al servidor
        fetch("api/admin/lecciones.php", {
          method: method,
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(leccionData),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Mostrar mensaje de éxito
              alert(data.message || (id ? "Lección actualizada correctamente" : "Lección creada correctamente"))

              // Redireccionar
              window.location.href = "lecciones.html"
            } else {
              // Mostrar mensaje de error
              alert(data.message || "Error al guardar la lección")

              // Restaurar botón
              submitBtn.disabled = false
              submitBtn.innerHTML = originalText
            }
          })
          .catch((error) => {
            console.error("Error:", error)
            alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")

            // Restaurar botón
            submitBtn.disabled = false
            submitBtn.innerHTML = originalText
          })
      }
    })
  } else {
    console.log("Formulario de lección NO encontrado")
  }

  // Función para cargar niveles
  function cargarNiveles() {
    fetch("api/admin/niveles.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const nivelSelect = document.getElementById("nivel")

          // Limpiar opciones existentes excepto la primera
          while (nivelSelect.options.length > 1) {
            nivelSelect.remove(1)
          }

          // Agregar nuevas opciones
          data.niveles.forEach((nivel) => {
            const option = document.createElement("option")
            option.value = nivel.id
            option.textContent = nivel.nombre
            nivelSelect.appendChild(option)
          })
        } else {
          console.error("Error al cargar niveles:", data.message)
        }
      })
      .catch((error) => {
        console.error("Error:", error)
      })
  }

  // Función para cargar categorías
  function cargarCategorias() {
    fetch("api/admin/categorias.php", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const categoriaSelect = document.getElementById("categoria")

          // Limpiar opciones existentes excepto la primera
          while (categoriaSelect.options.length > 1) {
            categoriaSelect.remove(1)
          }

          // Agregar nuevas opciones
          data.categorias.forEach((categoria) => {
            const option = document.createElement("option")
            option.value = categoria.id
            option.textContent = categoria.nombre
            categoriaSelect.appendChild(option)
          })
        } else {
          console.error("Error al cargar categorías:", data.message)
        }
      })
      .catch((error) => {
        console.error("Error:", error)
      })
  }

  // Función para cargar datos de una lección existente
  function cargarLeccion(id) {
    fetch(`api/admin/lecciones.php?id=${id}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const leccion = data.leccion

          // Llenar el formulario con los datos
          document.getElementById("id").value = leccion.id
          document.getElementById("titulo").value = leccion.titulo
          document.getElementById("descripcion").value = leccion.descripcion
          quill.root.innerHTML = leccion.contenido
          document.getElementById("nivel").value = leccion.nivel_id
          document.getElementById("categoria").value = leccion.categoria_id
        } else {
          console.error("Error al cargar la lección:", data.message)
          alert(`Error al cargar la lección: ${data.message}`)
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        alert("Error de conexión. Por favor, inténtalo de nuevo más tarde.")
      })
  }

  // Función para mostrar mensaje de error
  function showError(fieldId, message) {
    const field = document.getElementById(fieldId)
    if (!field) {
      console.warn(`Campo con ID ${fieldId} no encontrado`)
      return
    }

    let errorElement = document.getElementById(`${fieldId}-error`)

    // Si no existe el elemento de error, crearlo
    if (!errorElement) {
      errorElement = document.createElement("span")
      errorElement.className = "error-message"
      errorElement.id = `${fieldId}-error`
      field.parentNode.insertBefore(errorElement, field.nextSibling)
    }

    errorElement.textContent = message
  }

  // Función para limpiar todos los mensajes de error
  function clearErrors() {
    document.querySelectorAll(".error-message").forEach((el) => {
      el.textContent = ""
    })
  }

  // Función para añadir spans de error a todos los campos del formulario
  function addErrorSpans(form) {
    const inputs = form.querySelectorAll("input, select, textarea")
    inputs.forEach((input) => {
      if (input.type === "checkbox" || input.type === "radio" || input.type === "hidden") return

      const id = input.id
      if (!id) return

      // Verificar si ya existe un span de error
      let errorSpan = document.getElementById(`${id}-error`)
      if (!errorSpan) {
        errorSpan = document.createElement("span")
        errorSpan.className = "error-message"
        errorSpan.id = `${id}-error`
        input.parentNode.insertBefore(errorSpan, input.nextSibling)
      }
    })
  }
})

