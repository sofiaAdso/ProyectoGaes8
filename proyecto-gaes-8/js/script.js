document.addEventListener("DOMContentLoaded", () => {
  // Initialize Lucide icons
  if (typeof window.lucide !== "undefined") {
    window.lucide.createIcons()
  } else {
    console.warn("Lucide is not defined. Ensure it is properly imported.")
  }

  // Dropdown functionality
  const dropdownTriggers = document.querySelectorAll(".dropdown-trigger")

  dropdownTriggers.forEach((trigger) => {
    trigger.addEventListener("click", function (e) {
      e.preventDefault()
      // Here you would toggle the visibility of the dropdown menu
      console.log("Dropdown clicked:", this.textContent)
    })
  })

  // Play button functionality
  const playButtons = document.querySelectorAll(".play-button")

  playButtons.forEach((button) => {
    button.addEventListener("click", () => {
      // Here you would implement the video play functionality
      console.log("Play button clicked")
    })
  })

  // User button functionality
  const userButton = document.querySelector(".user-button")

  // Verificar si el elemento existe antes de añadir el event listener
  if (userButton) {
    userButton.addEventListener("click", () => {
      // Here you would implement the user menu functionality
      console.log("User button clicked")
    })
  }

  // Verificar si el usuario está autenticado
  verificarSesion()

  // Función para verificar la sesión del usuario
  function verificarSesion() {
    // Verificar si hay datos en sessionStorage
    if (typeof sessionStorage !== "undefined") {
      const usuarioId = sessionStorage.getItem("usuario_id")

      if (usuarioId) {
        // Verificar la sesión con el servidor
        fetch("api/auth.php", {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Usuario autenticado
              mostrarMenuUsuario(data.usuario.nombre, data.usuario.rol)
            } else {
              // Sesión inválida, limpiar sessionStorage
              sessionStorage.removeItem("usuario_id")
              sessionStorage.removeItem("usuario_nombre")
              sessionStorage.removeItem("usuario_email")
              sessionStorage.removeItem("usuario_rol")
              mostrarMenuInvitado()
            }
          })
          .catch((error) => {
            console.error("Error al verificar sesión:", error)
            // En caso de error, usar datos de sessionStorage
            const usuarioNombre = sessionStorage.getItem("usuario_nombre")
            const usuarioRol = sessionStorage.getItem("usuario_rol")
            if (usuarioNombre) {
              mostrarMenuUsuario(usuarioNombre, usuarioRol)
            } else {
              mostrarMenuInvitado()
            }
          })
      } else {
        // No hay datos en sessionStorage, mostrar menú de invitado
        mostrarMenuInvitado()
      }
    } else {
      // SessionStorage no disponible, mostrar menú de invitado
      mostrarMenuInvitado()
    }
  }

  // Función para mostrar el menú de usuario autenticado
  function mostrarMenuUsuario(nombre, rol) {
    const menuInvitado = document.getElementById("guest-menu")
    const menuUsuario = document.getElementById("user-menu")
    const menuAdmin = document.getElementById("admin-menu")

    if (menuInvitado) menuInvitado.style.display = "none"
    if (menuUsuario) {
      menuUsuario.style.display = "block"

      // Actualizar nombre de usuario si existe el elemento
      const userDropdown = document.getElementById("user-dropdown")
      if (userDropdown) {
        userDropdown.textContent = nombre
      }
    }

    // Mostrar menú de administración si el rol es admin
    if (menuAdmin && (rol === "admin_general" || rol === "admin_contenidos")) {
      menuAdmin.style.display = "block"
    }
  }

  // Función para mostrar el menú de invitado
  function mostrarMenuInvitado() {
    const menuInvitado = document.getElementById("guest-menu")
    const menuUsuario = document.getElementById("user-menu")
    const menuAdmin = document.getElementById("admin-menu")

    if (menuInvitado) menuInvitado.style.display = "block"
    if (menuUsuario) menuUsuario.style.display = "none"
    if (menuAdmin) menuAdmin.style.display = "none"
  }

  // Agregar funcionalidad al menú desplegable de usuario
  const userDropdown = document.getElementById("user-dropdown")
  const dropdownMenu = document.querySelector(".dropdown-menu")

  if (userDropdown && dropdownMenu) {
    userDropdown.addEventListener("click", (e) => {
      e.preventDefault()
      dropdownMenu.classList.toggle("active")
    })

    // Cerrar el menú al hacer clic fuera de él
    document.addEventListener("click", (e) => {
      if (!userDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove("active")
      }
    })
  }
})

