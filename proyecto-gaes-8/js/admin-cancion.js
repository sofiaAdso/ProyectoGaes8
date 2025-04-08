document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Manejar la vista previa de la tablatura
  const tablaturaInput = document.getElementById("tablatura")
  const tablaturaPreview = document.getElementById("tablatura-preview")

  tablaturaInput.addEventListener("change", function () {
    const file = this.files[0]
    if (file) {
      const reader = new FileReader()

      reader.onload = (e) => {
        const fileType = file.type

        // Limpiar vista previa anterior
        tablaturaPreview.innerHTML = ""

        if (fileType.startsWith("image/")) {
          // Si es una imagen, mostrar vista previa
          const img = document.createElement("img")
          img.src = e.target.result
          img.style.maxWidth = "100%"
          img.style.maxHeight = "200px"
          tablaturaPreview.appendChild(img)
        } else if (fileType === "application/pdf") {
          // Si es un PDF, mostrar un enlace
          const link = document.createElement("a")
          link.href = e.target.result
          link.textContent = "Ver PDF"
          link.target = "_blank"
          tablaturaPreview.appendChild(link)
        }
      }

      reader.readAsDataURL(file)
    }
  })

  // Verificar si estamos editando una canción existente
  const urlParams = new URLSearchParams(window.location.search)
  const cancionId = urlParams.get("id")

  if (cancionId) {
    // Cambiar el título del formulario
    document.getElementById("form-title").textContent = "Editar Canción"

    // Mock song data for editing
    const mockCancion = {
      id: cancionId,
      titulo: "Wonderwall",
      artista: "Oasis",
      descripcion: "Una de las canciones más populares para aprender en guitarra acústica.",
      video_url: "https://www.youtube.com/watch?v=bx1Bh8ZvH84",
      nivel_nombre: "principiante",
      tablatura: "/placeholder.svg?height=300&width=200",
    }

    // Llenar el formulario con los datos
    document.getElementById("id").value = mockCancion.id
    document.getElementById("titulo").value = mockCancion.titulo
    document.getElementById("artista").value = mockCancion.artista
    document.getElementById("descripcion").value = mockCancion.descripcion
    document.getElementById("video_url").value = mockCancion.video_url
    document.getElementById("nivel").value = mockCancion.nivel_nombre

    // Si hay una tablatura, mostrar un enlace o vista previa
    if (mockCancion.tablatura) {
      const tablaturaPath = mockCancion.tablatura
      const extension = tablaturaPath.split(".").pop().toLowerCase()

      if (["jpg", "jpeg", "png", "svg"].includes(extension)) {
        const img = document.createElement("img")
        img.src = tablaturaPath
        img.style.maxWidth = "100%"
        img.style.maxHeight = "200px"
        tablaturaPreview.appendChild(img)
      } else if (extension === "pdf") {
        const link = document.createElement("a")
        link.href = tablaturaPath
        link.textContent = "Ver PDF actual"
        link.target = "_blank"
        tablaturaPreview.appendChild(link)
      }
    }
  }

  // Validación del formulario de canción
  const cancionForm = document.getElementById("cancion-form")
  if (cancionForm) {
    console.log("Formulario de canción encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(cancionForm)

    cancionForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de canción enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const id = document.getElementById("id").value
      const titulo = document.getElementById("titulo").value.trim()
      const artista = document.getElementById("artista").value.trim()
      const descripcion = document.getElementById("descripcion").value.trim()
      const video_url = document.getElementById("video_url").value.trim()
      const nivel = document.getElementById("nivel").value
      const tablatura = document.getElementById("tablatura").files[0]

      let hasErrors = false

      // Validar título
      if (!titulo) {
        showError("titulo", "El título es obligatorio")
        hasErrors = true
      }

      // Validar artista
      if (!artista) {
        showError("artista", "El artista es obligatorio")
        hasErrors = true
      }

      // Validar descripción
      if (!descripcion) {
        showError("descripcion", "La descripción es obligatoria")
        hasErrors = true
      }

      // Validar nivel
      if (!nivel) {
        showError("nivel", "El nivel es obligatorio")
        hasErrors = true
      }

      // Si no hay errores, simular envío exitoso
      if (!hasErrors) {
        console.log("Enviando datos de canción al servidor")

        // Simulate successful submission
        setTimeout(() => {
          alert(id ? "Canción actualizada correctamente" : "Canción creada correctamente")
          window.location.href = "canciones.html"
        }, 1000)
      }
    })
  } else {
    console.log("Formulario de canción NO encontrado")
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

