document.addEventListener("DOMContentLoaded", () => {
  const bpmDisplay = document.getElementById("bpm-value")
  const bpmSlider = document.getElementById("bpm-slider")
  const tempoText = document.getElementById("tempo-text")
  const decreaseTempo = document.getElementById("decrease-tempo")
  const increaseTempo = document.getElementById("increase-tempo")
  const startStopBtn = document.getElementById("start-stop")
  const startStopText = document.getElementById("start-stop-text")
  const playIcon = document.getElementById("play-icon")
  const beatsPerMeasure = document.getElementById("beats-per-measure")
  const decreaseBeats = document.getElementById("decrease-beats")
  const increaseBeats = document.getElementById("increase-beats")

  let bpm = 120
  let beatsPerMeasureValue = 4
  let isPlaying = false
  let timer
  let count = 0

  // Añadir el mapa de tempos
  const tempoTextMap = {
    40: "Largo",
    60: "Adagio",
    76: "Andante",
    108: "Moderato",
    120: "Allegro",
    168: "Vivace",
    200: "Presto",
  }

  function updateMetronome() {
    clearInterval(timer)
    if (isPlaying) {
      timer = setInterval(playClick, (60 / bpm) * 1000)
    }
  }

  function playClick() {
    const click = new Audio("data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAESsAABErAAABAAgAZGF0YQAAAAA=")
    click.play()
    count++
    if (count > beatsPerMeasureValue) {
      count = 1
    }
  }

  function updateBPMDisplay() {
    bpmDisplay.textContent = bpm
    const closestTempo = Object.keys(tempoTextMap).reduce((a, b) => {
      return Math.abs(b - bpm) < Math.abs(a - bpm) ? b : a
    })
    tempoText.textContent = tempoTextMap[closestTempo]
  }

  bpmSlider.addEventListener("input", function () {
    bpm = Number.parseInt(this.value)
    updateBPMDisplay()
    updateMetronome()
  })

  decreaseTempo.addEventListener("click", () => {
    bpm = Math.max(40, bpm - 1)
    bpmSlider.value = bpm
    updateBPMDisplay()
    updateMetronome()
  })

  increaseTempo.addEventListener("click", () => {
    bpm = Math.min(220, bpm + 1)
    bpmSlider.value = bpm
    updateBPMDisplay()
    updateMetronome()
  })

  startStopBtn.addEventListener("click", () => {
    isPlaying = !isPlaying
    if (isPlaying) {
      count = 0
      updateMetronome()
      startStopText.textContent = "Detener"
      playIcon.setAttribute("data-lucide", "pause")
    } else {
      clearInterval(timer)
      startStopText.textContent = "Iniciar"
      playIcon.setAttribute("data-lucide", "play")
    }
    if (typeof lucide !== "undefined") {
      lucide.createIcons()
    }
  })

  decreaseBeats.addEventListener("click", () => {
    beatsPerMeasureValue = Math.max(2, beatsPerMeasureValue - 1)
    beatsPerMeasure.textContent = beatsPerMeasureValue
  })

  increaseBeats.addEventListener("click", () => {
    beatsPerMeasureValue = Math.min(12, beatsPerMeasureValue + 1)
    beatsPerMeasure.textContent = beatsPerMeasureValue
  })

  updateBPMDisplay()
  if (typeof lucide !== "undefined") {
    lucide.createIcons()
  }
})

