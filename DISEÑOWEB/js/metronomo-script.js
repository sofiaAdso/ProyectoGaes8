document.addEventListener('DOMContentLoaded', function() {
    const bpmDisplay = document.getElementById('bpm-value');
    const bpmSlider = document.getElementById('bpm-slider');
    const tempoText = document.getElementById('tempo-text');
    const decreaseTempo = document.getElementById('decrease-tempo');
    const increaseTempo = document.getElementById('increase-tempo');
    const startStopBtn = document.getElementById('start-stop');
    const startStopText = document.getElementById('start-stop-text');
    const playIcon = document.getElementById('play-icon');
    const beatsPerMeasure = document.getElementById('beats-per-measure');
    const decreaseBeats = document.getElementById('decrease-beats');
    const increaseBeats = document.getElementById('increase-beats');

    let bpm = 120;
    let beatsPerMeasureValue = 4;
    let isPlaying = false;
    let timer;
    let count = 0;

   

    function updateMetronome() {
        clearInterval(timer);
        if (isPlaying) {
            timer = setInterval(playClick, (60 / bpm) * 1000);
        }
    }

    function playClick() {
        const click = new Audio('data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAESsAABErAAABAAgAZGF0YQAAAAA=');
        click.play();
        count++;
        if (count > beatsPerMeasureValue) {
            count = 1;
        }
    }

    function updateBPMDisplay() {
        bpmDisplay.textContent = bpm;
        let closestTempo = Object.keys(tempoTextMap).reduce((a, b) => {
            return Math.abs(b - bpm) < Math.abs(a - bpm) ? b : a;
        });
        tempoText.textContent = tempoTextMap[closestTempo];
    }

    bpmSlider.addEventListener('input', function() {
        bpm = parseInt(this.value);
        updateBPMDisplay();
        updateMetronome();
    });

    decreaseTempo.addEventListener('click', function() {
        bpm = Math.max(40, bpm - 1);
        bpmSlider.value = bpm;
        updateBPMDisplay();
        updateMetronome();
    });

    increaseTempo.addEventListener('click', function() {
        bpm = Math.min(220, bpm + 1);
        bpmSlider.value = bpm;
        updateBPMDisplay();
        updateMetronome();
    });

    startStopBtn.addEventListener('click', function() {
        isPlaying = !isPlaying;
        if (isPlaying) {
            count = 0;
            updateMetronome();
            startStopText.textContent = 'Detener';
            playIcon.setAttribute('data-lucide', 'pause');
        } else {
            clearInterval(timer);
            startStopText.textContent = 'Iniciar';
            playIcon.setAttribute('data-lucide', 'play');
        }
        lucide.createIcons();
    });

    decreaseBeats.addEventListener('click', function() {
        beatsPerMeasureValue = Math.max(2, beatsPerMeasureValue - 1);
        beatsPerMeasure.textContent = beatsPerMeasureValue;
    });

    increaseBeats.addEventListener('click', function() {
        beatsPerMeasureValue = Math.min(12, beatsPerMeasureValue + 1);
        beatsPerMeasure.textContent = beatsPerMeasureValue;
    });

    updateBPMDisplay();
    lucide.createIcons();
});

