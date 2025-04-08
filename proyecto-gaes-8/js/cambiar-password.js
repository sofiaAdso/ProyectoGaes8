document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Validación del formulario de cambio de contraseña
  const passwordForm = document.getElementById("cambiar-password-form")
  if (passwordForm) {
    console.log("Formulario de cambio de contraseña encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(passwordForm)

    passwordForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de cambio de contraseña enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const passwordActual = document.getElementById("password_actual").value
      const passwordNuevo = document.getElementById("password_nuevo").value
      const passwordConfirmar = document.getElementById("password_confirmar").value

      let hasErrors = false

      // Validar contraseña actual
      if (!passwordActual) {
        showError("password_actual", "La contraseña actual es obligatoria")
        hasErrors = true
      }

      // Validar nueva contraseña
      if (!passwordNuevo) {
        showError("password_nuevo", "La nueva contraseña es obligatoria")
        hasErrors = true
      } else if (passwordNuevo.length < 6) {
        showError("password_nuevo", "La nueva contraseña debe tener al menos 6 caracteres")
        hasErrors = true
      }

      // Validar confirmación de contraseña
      if (passwordNuevo !== passwordConfirmar) {
        showError("password_confirmar", "Las contraseñas no coinciden")
        hasErrors = true
      }

      // Si no hay errores, enviar el formulario mediante fetch
      if (!hasErrors) {
        console.log("Enviando datos de cambio de contraseña al servidor")

        // Mostrar indicador de carga
        const submitBtn = passwordForm.querySelector('button[type="submit"]')
        const originalText = submitBtn.innerHTML
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner"></span> Actualizando...'

        // Enviar datos al servidor
        fetch("api/perfil.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            action: "change_password",
            password_actual: passwordActual,
            password_nuevo: passwordNuevo,
            password_confirmar: passwordConfirmar,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Mostrar mensaje de éxito
              alert(data.message || "Contraseña actualizada correctamente")

              // Redireccionar
              window.location.href = data.redirect || "perfil.html"
            } else {
              // Mostrar mensaje de error
              alert(data.message || "Error al actualizar la contraseña")

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
    console.log("Formulario de cambio de contraseña NO encontrado")
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

