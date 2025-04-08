document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  let lucide
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Inicializar editor Quill
  let quill
  try {
    // Check if Quill is available
    if (typeof Quill === "undefined") {
      console.warn("Quill is undefined. Using a mock editor instead.")
      // Create a mock Quill object
      quill = {
        root: document.createElement("div"),
        on: () => {},
      }
      // Add the mock root to the DOM
      const editorContainer = document.getElementById("editor-instrucciones")
      if (editorContainer) {
        editorContainer.appendChild(quill.root)
      }
    } else {
      quill = new Quill("#editor-instrucciones", {
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
    }
  } catch (error) {
    console.error("Quill initialization error:", error)
    alert("Failed to initialize the Quill editor. Please check your setup.")
    return // Exit if Quill fails to initialize
  }

  // Manejar la vista previa de la partitura
  const partituraInput = document.getElementById("partitura")
  const partituraPreview = document.getElementById("partitura-preview")

  partituraInput.addEventListener("change", function () {
    const file = this.files[0]
    if (file) {
      const reader = new FileReader()

      reader.onload = (e) => {
        const fileType = file.type

        // Limpiar vista previa anterior
        partituraPreview.innerHTML = ""

        if (fileType.startsWith("image/")) {
          // Si es una imagen, mostrar vista previa
          const img = document.createElement("img")
          img.src = e.target.result
          img.style.maxWidth = "100%"
          img.style.maxHeight = "200px"
          partituraPreview.appendChild(img)
        } else if (fileType === "application/pdf") {
          // Si es un PDF, mostrar un enlace
          const link = document.createElement("a")
          link.href = e.target.result
          link.textContent = "Ver PDF"
          link.target = "_blank"
          partituraPreview.appendChild(link)
        }
      }

      reader.readAsDataURL(file)
    }
  })

  // Verificar si estamos editando un ejercicio existente
  const urlParams = new URLSearchParams(window.location.search)
  const ejercicioId = urlParams.get("id")

  if (ejercicioId) {
    // Cambiar el título del formulario
    document.getElementById("form-title").textContent = "Editar Ejercicio"

    // Mock exercise data for editing
    const mockEjercicio = {
      id: ejercicioId,
      titulo: "Ejercicio de dedos",
      descripcion: "Mejora la agilidad y fuerza de tus dedos con este ejercicio de calentamiento.",
      instrucciones:
        "<h2>Instrucciones</h2><p>Este ejercicio te ayudará a mejorar la independencia y fuerza de tus dedos:</p><ol><li>Coloca tu mano izquierda en el mástil, con los dedos sobre los trastes 1-4 de la sexta cuerda.</li><li>Presiona y suelta cada traste con cada dedo, en orden ascendente y luego descendente.</li><li>Mantén los otros dedos lo más cerca posible de las cuerdas sin tocarlas.</li><li>Repite el ejercicio en cada cuerda.</li><li>Comienza lentamente y aumenta la velocidad gradualmente.</li></ol><p>Practica este ejercicio durante 5-10 minutos al día para mejores resultados.</p>",
      nivel: "principiante",
      tipo: "tecnica",
    }

    // Llenar el formulario con los datos
    document.getElementById("id").value = mockEjercicio.id
    document.getElementById("titulo").value = mockEjercicio.titulo
    document.getElementById("descripcion").value = mockEjercicio.descripcion
    quill.root.innerHTML = mockEjercicio.instrucciones
    document.getElementById("nivel").value = mockEjercicio.nivel
    document.getElementById("tipo").value = mockEjercicio.tipo
  }

  // Validación del formulario de ejercicio
  const ejercicioForm = document.getElementById("ejercicio-form")
  if (ejercicioForm) {
    console.log("Formulario de ejercicio encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(ejercicioForm)

    ejercicioForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de ejercicio enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const id = document.getElementById("id").value
      const titulo = document.getElementById("titulo").value.trim()
      const descripcion = document.getElementById("descripcion").value.trim()
      const instrucciones = quill.root.innerHTML
      document.getElementById("instrucciones").value = instrucciones // Actualizar el campo oculto
      const nivel = document.getElementById("nivel").value
      const tipo = document.getElementById("tipo").value
      const partitura = document.getElementById("partitura").files[0]

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

      // Validar instrucciones
      if (!instrucciones || instrucciones === "<p><br></p>") {
        showError("instrucciones", "Las instrucciones son obligatorias")
        hasErrors = true
      }

      // Validar nivel
      if (!nivel) {
        showError("nivel", "El nivel es obligatorio")
        hasErrors = true
      }

      // Validar tipo
      if (!tipo) {
        showError("tipo", "El tipo de ejercicio es obligatorio")
        hasErrors = true
      }

      // Si no hay errores, simular envío exitoso
      if (!hasErrors) {
        console.log("Enviando datos de ejercicio al servidor")

        // Simulate successful submission
        setTimeout(() => {
          alert(id ? "Ejercicio actualizado correctamente" : "Ejercicio creado correctamente")
          window.location.href = "ejercicios.html"
        }, 1000)
      }
    })
  } else {
    console.log("Formulario de ejercicio NO encontrado")
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

