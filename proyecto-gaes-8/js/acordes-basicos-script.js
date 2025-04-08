document.addEventListener("DOMContentLoaded", () => {
  const acordeItems = document.querySelectorAll(".acorde-item")
  const acordeInfo = document.getElementById("acorde-info")
  const acordeAudio = document.getElementById("acorde-audio")
  const acordeTips = document.getElementById("acorde-tips")
  const startPracticeBtn = document.getElementById("start-practice")
  const practiceDisplay = document.getElementById("practice-display")
  const currentChordDisplay = document.getElementById("current-chord")
  const nextChordDisplay = document.getElementById("next-chord")
  const timerDisplay = document.getElementById("timer")

  const acordesData = {
    A: {
      nombre: "La Mayor",
      descripcion: "El acorde A se forma colocando los dedos en el segundo traste de las cuerdas 2, 3 y 4.",
      audioSrc: "/audio/a-major.mp3",
      tips: "Asegúrate de que todos los dedos estén cerca del traste para obtener un sonido limpio.",
    },
    D: {
      nombre: "Re Mayor",
      descripcion:
        "Para formar el acorde D, coloca los dedos en el segundo traste de la cuerda 1, el tercer traste de la cuerda 2 y el segundo traste de la cuerda 3.",
      audioSrc: "/audio/d-major.mp3",
      tips: "Mantén los dedos arqueados para evitar tocar otras cuerdas.",
    },
    G: {
      nombre: "Sol Mayor",
      descripcion:
        "El acorde G se forma colocando los dedos en el tercer traste de la cuerda 1, el segundo traste de la cuerda 5 y el tercer traste de la cuerda 6.",
      audioSrc: "/audio/g-major.mp3",
      tips: "Este acorde puede ser difícil al principio. Practica el cambio lentamente.",
    },
    Am: {
      nombre: "La menor",
      descripcion: "Para formar Am, coloca el dedo en el primer traste de la cuerda 2.",
      audioSrc: "/audio/a-minor.mp3",
      tips: "Este es uno de los acordes más fáciles. Úsalo como punto de referencia.",
    },
    Em: {
      nombre: "Mi menor",
      descripcion: "El acorde Em se forma colocando los dedos en el segundo traste de las cuerdas 4 y 5.",
      audioSrc: "/audio/e-minor.mp3",
      tips: "Asegúrate de que las cuerdas 1, 2, 3 y 6 suenen abiertas.",
    },
  }

  acordeItems.forEach((item) => {
    item.addEventListener("click", function () {
      const acorde = this.getAttribute("data-acorde")
      const acordeData = acordesData[acorde]

      acordeInfo.innerHTML = `
                <h3>${acordeData.nombre}</h3>
                <p>${acordeData.descripcion}</p>
            `

      acordeAudio.innerHTML = `
    <p>Audio de demostración no disponible en esta versión.</p>
    <button class="btn btn-secondary">Reproducir acorde (simulado)</button>
`

      acordeTips.innerHTML = `
                <h4>Consejos:</h4>
                <p>${acordeData.tips}</p>
            `
    })
  })

  let practiceInterval
  let practiceTime = 0
  const acordes = ["A", "D", "G", "Am", "Em"]

  startPracticeBtn.addEventListener("click", function () {
    if (this.textContent === "Iniciar Práctica") {
      startPractice()
      this.textContent = "Detener Práctica"
    } else {
      stopPractice()
      this.textContent = "Iniciar Práctica"
    }
  })

  function startPractice() {
    practiceDisplay.classList.remove("hidden")
    practiceTime = 0
    updateChord()
    practiceInterval = setInterval(() => {
      practiceTime++
      timerDisplay.textContent = `Tiempo: ${practiceTime}s`
      if (practiceTime % 5 === 0) {
        updateChord()
      }
    }, 1000)
  }

  function stopPractice() {
    clearInterval(practiceInterval)
    practiceDisplay.classList.add("hidden")
    currentChordDisplay.textContent = "-"
    nextChordDisplay.textContent = "Siguiente: -"
    timerDisplay.textContent = "Tiempo: 0s"
  }

  function updateChord() {
    const currentChord = currentChordDisplay.textContent
    let nextChord
    do {
      nextChord = acordes[Math.floor(Math.random() * acordes.length)]
    } while (nextChord === currentChord)

    currentChordDisplay.textContent = nextChord
    nextChordDisplay.textContent = `Siguiente: ${acordes[Math.floor(Math.random() * acordes.length)]}`
  }

  // Inicializar los íconos de Lucide
  const lucide = window.lucide
  lucide.createIcons()
})

