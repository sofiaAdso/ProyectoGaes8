document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Lucide icons
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  } else {
    console.warn("Lucide icons not initialized: lucide is undefined.")
  }

  // Verificar si hay un mensaje de cierre de sesión
  const urlParams = new URLSearchParams(window.location.search)
  if (urlParams.get("logout") === "success") {
    alert("Has cerrado sesión correctamente.")
  }
  if (urlParams.get("registro") === "exitoso") {
    alert("¡Registro exitoso! Ahora puedes iniciar sesión.")
  }

  // Validación del formulario de inicio de sesión
  const loginForm = document.getElementById("login-form")
  if (loginForm) {
    console.log("Formulario de login encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(loginForm)

    loginForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de login enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const email = document.getElementById("email").value.trim()
      const password = document.getElementById("password").value

      let hasErrors = false

      // Validar email
      if (!email) {
        showError("email", "El correo electrónico es obligatorio")
        hasErrors = true
      } else if (!isValidEmail(email)) {
        showError("email", "Formato de correo electrónico inválido")
        hasErrors = true
      }

      // Validar contraseña
      if (!password) {
        showError("password", "La contraseña es obligatoria")
        hasErrors = true
      }

      // Si no hay errores, enviar el formulario mediante fetch
      if (!hasErrors) {
        console.log("Enviando datos de login al servidor")

        // Mostrar indicador de carga
        const submitBtn = loginForm.querySelector('button[type="submit"]')
        const originalText = submitBtn.innerHTML
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner"></span> Iniciando sesión...'

        // Enviar datos al servidor
        fetch("api/auth.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            action: "login",
            email: email,
            password: password,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Guardar datos del usuario en sessionStorage
              sessionStorage.setItem("usuario_id", data.usuario.id)
              sessionStorage.setItem("usuario_nombre", data.usuario.nombre)
              sessionStorage.setItem("usuario_email", data.usuario.email)
              sessionStorage.setItem("usuario_rol", data.usuario.rol)

              // Mostrar mensaje de éxito
              alert(data.message || "Inicio de sesión exitoso")

              // Redireccionar
              window.location.href = data.redirect || "index.html"
            } else {
              // Mostrar mensaje de error
              alert(data.message || "Error al iniciar sesión")

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
    console.log("Formulario de login NO encontrado")
  }

  // Validación del formulario de registro
  const registerForm = document.getElementById("register-form")
  if (registerForm) {
    console.log("Formulario de registro encontrado")

    // Asegurarse de que cada campo tenga un span para mensajes de error
    addErrorSpans(registerForm)

    registerForm.addEventListener("submit", (e) => {
      e.preventDefault()
      console.log("Formulario de registro enviado")

      // Resetear mensajes de error
      clearErrors()

      // Obtener valores
      const name = document.getElementById("name").value.trim()
      const email = document.getElementById("email").value.trim()
      const password = document.getElementById("password").value
      const confirmPassword = document.getElementById("confirm-password").value
      const isAdmin = document.getElementById("is-admin") ? document.getElementById("is-admin").checked : false

      let hasErrors = false

      // Validar nombre
      if (!name) {
        showError("name", "El nombre es obligatorio")
        hasErrors = true
      } else if (name.length < 3) {
        showError("name", "El nombre debe tener al menos 3 caracteres")
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

      // Validar contraseña
      if (!password) {
        showError("password", "La contraseña es obligatoria")
        hasErrors = true
      } else if (password.length < 6) {
        showError("password", "La contraseña debe tener al menos 6 caracteres")
        hasErrors = true
      }

      // Validar confirmación de contraseña
      if (password !== confirmPassword) {
        showError("confirm-password", "Las contraseñas no coinciden")
        hasErrors = true
      }

      // Si no hay errores, enviar el formulario mediante fetch
      if (!hasErrors) {
        console.log("Enviando datos de registro al servidor")

        // Mostrar indicador de carga
        const submitBtn = registerForm.querySelector('button[type="submit"]')
        const originalText = submitBtn.innerHTML
        submitBtn.disabled = true
        submitBtn.innerHTML = '<span class="spinner"></span> Registrando...'

        // Enviar datos al servidor
        fetch("api/auth.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            action: "register",
            nombre: name,
            email: email,
            password: password,
            rol: isAdmin ? "admin_contenidos" : "aprendiz",
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Mostrar mensaje de éxito
              alert(data.message || "Registro exitoso")

              // Redireccionar
              window.location.href = data.redirect || "login.html?registro=exitoso"
            } else {
              // Mostrar mensaje de error
              alert(data.message || "Error al registrar")

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
    console.log("Formulario de registro NO encontrado")
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

