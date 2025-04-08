document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Validación del formulario de contacto
  const contactoForm = document.getElementById("contacto-form")
  if (contactoForm) {
    console.log("Formulario de contacto encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(contactoForm)

    contactoForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de contacto enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const nombre = document.getElementById("nombre").value.trim()
      const email = document.getElementById("email").value.trim()
      const asunto = document.getElementById("asunto").value.trim()
      const mensaje = document.getElementById("mensaje").value.trim()

      let hasErrors = false

      // Validar nombre
      if (!nombre) {
        showError("nombre", "El nombre es obligatorio")
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

      // Validar asunto
      if (!asunto) {
        showError("asunto", "El asunto es obligatorio")
        hasErrors = true
      }

      // Validar mensaje
      if (!mensaje) {
        showError("mensaje", "El mensaje es obligatorio")
        hasErrors = true
      }

      // Si no hay errores, enviar el formulario mediante fetch
      if (!hasErrors) {
        console.log("Enviando datos de contacto al servidor")

        // Mostrar indicador de carga
        const submitBtn = contactoForm.querySelector('button[type="submit"]')
        const originalText = submitBtn.innerHTML
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner"></span> Enviando...'

        // Enviar datos al servidor
        fetch("api/contacto.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            nombre: nombre,
            email: email,
            asunto: asunto,
            mensaje: mensaje,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Mostrar mensaje de éxito
              alert(data.message || "Mensaje enviado correctamente")

              // Redireccionar
              window.location.href = data.redirect || "index.html"
            } else {
              // Mostrar mensaje de error
              alert(data.message || "Error al enviar el mensaje")

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
    console.log("Formulario de contacto NO encontrado")
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
      if (input.type === "checkbox" || input.type === "radio") return

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

