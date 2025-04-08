document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Manejar la vista previa del avatar
  const avatarInput = document.getElementById("avatar")
  const avatarPreview = document.getElementById("avatar-preview")

  if (avatarInput && avatarPreview) {
    avatarInput.addEventListener("change", function () {
      const file = this.files[0]
      if (file) {
        const reader = new FileReader()

        reader.onload = (e) => {
          // Limpiar vista previa anterior
          avatarPreview.innerHTML = ""

          // Crear imagen de vista previa
          const img = document.createElement("img")
          img.src = e.target.result
          img.style.maxWidth = "100px"
          img.style.maxHeight = "100px"
          img.style.borderRadius = "50%"
          img.style.objectFit = "cover"
          avatarPreview.appendChild(img)
        }

        reader.readAsDataURL(file)
      }
    })
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
        // Llenar el formulario con los datos del usuario
        document.getElementById("nombre").value = data.usuario.nombre
        document.getElementById("email").value = data.usuario.email

        if (data.usuario.biografia) {
          document.getElementById("biografia").value = data.usuario.biografia
        }

        // Si hay avatar, mostrar vista previa
        if (data.usuario.avatar) {
          const img = document.createElement("img")
          img.src = data.usuario.avatar
          img.style.maxWidth = "100px"
          img.style.maxHeight = "100px"
          img.style.borderRadius = "50%"
          img.style.objectFit = "cover"
          avatarPreview.appendChild(img)
        } else {
          // Mostrar avatar por defecto
          const img = document.createElement("img")
          img.src = "/placeholder.svg?height=100&width=100"
          img.style.maxWidth = "100px"
          img.style.maxHeight = "100px"
          img.style.borderRadius = "50%"
          img.style.objectFit = "cover"
          avatarPreview.appendChild(img)
        }
      } else {
        console.error("Error al cargar el perfil:", data.message)
        // Si hay error, usar datos de sessionStorage como respaldo
        const nombre = sessionStorage.getItem("usuario_nombre") || "Usuario"
        const email = sessionStorage.getItem("usuario_email") || "usuario@example.com"

        document.getElementById("nombre").value = nombre
        document.getElementById("email").value = email

        // Mostrar avatar por defecto
        const img = document.createElement("img")
        img.src = "/placeholder.svg?height=100&width=100"
        img.style.maxWidth = "100px"
        img.style.maxHeight = "100px"
        img.style.borderRadius = "50%"
        img.style.objectFit = "cover"
        avatarPreview.appendChild(img)
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      // Si hay error, usar datos de sessionStorage como respaldo
      const nombre = sessionStorage.getItem("usuario_nombre") || "Usuario"
      const email = sessionStorage.getItem("usuario_email") || "usuario@example.com"

      document.getElementById("nombre").value = nombre
      document.getElementById("email").value = email

      // Mostrar avatar por defecto
      const img = document.createElement("img")
      img.src = "/placeholder.svg?height=100&width=100"
      img.style.maxWidth = "100px"
      img.style.maxHeight = "100px"
      img.style.borderRadius = "50%"
      img.style.objectFit = "cover"
      avatarPreview.appendChild(img)
    })

  // Validación del formulario de edición de perfil
  const perfilForm = document.getElementById("editar-perfil-form")
  if (perfilForm) {
    console.log("Formulario de edición de perfil encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(perfilForm)

    perfilForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de edición de perfil enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const nombre = document.getElementById("nombre").value.trim()
      const email = document.getElementById("email").value.trim()
      const biografia = document.getElementById("biografia").value.trim()
      const avatar = document.getElementById("avatar").files[0]

      let hasErrors = false

      // Validar nombre
      if (!nombre) {
        showError("nombre", "El nombre es obligatorio")
        hasErrors = true
      } else if (nombre.length < 3) {
        showError("nombre", "El nombre debe tener al menos 3 caracteres")
        hasErrors = true
      }

      // Validar email
      if (!email) {
        showError("email", "El correo electrónico es obligatorio")
        hasErrors = true
      } else if (!isValidEmail(email)) {
        showError("email", "Formato de correo electrónico inválido")
        hasErrors = true
      }

      // Si no hay errores, enviar el formulario mediante FormData para manejar archivos
      if (!hasErrors) {
        console.log("Enviando datos de perfil al servidor")

        // Mostrar indicador de carga
        const submitBtn = perfilForm.querySelector('button[type="submit"]')
        const originalText = submitBtn.innerHTML
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner"></span> Guardando...'

        // Crear FormData para enviar archivos
        const formData = new FormData()
        formData.append("action", "update_profile")
        formData.append("nombre", nombre)
        formData.append("email", email)
        formData.append("biografia", biografia)

        if (avatar) {
          formData.append("avatar", avatar)
        }

        // Enviar datos al servidor
        fetch("api/perfil.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Actualizar datos en sessionStorage
              sessionStorage.setItem("usuario_nombre", nombre)
              sessionStorage.setItem("usuario_email", email)

              // Mostrar mensaje de éxito
              alert(data.message || "Perfil actualizado correctamente")

              // Redireccionar
              window.location.href = data.redirect || "perfil.html"
            } else {
              // Mostrar mensaje de error
              alert(data.message || "Error al actualizar el perfil")

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
    console.log("Formulario de edición de perfil NO encontrado")
  }

  // Función para validar email
  function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email)
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
      if (input.type === "checkbox" || input.type === "radio" || input.type === "file") return

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

