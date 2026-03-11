const soundQueue = [];
let isSpeaking = false;

const SOUNDS_BASE = (typeof window !== 'undefined' ? window.location.origin : '') + '/sounds';

async function processQueue() {
    if (isSpeaking || soundQueue.length === 0) return;

    isSpeaking = true;

    const message = soundQueue.shift();
    try {
        await playQueueSound(message);
    } catch (e) {
        console.warn('Queue sound error:', e);
    }
    isSpeaking = false;
    processQueue();
}

async function playSoundFile(src) {
    return new Promise((resolve, reject) => {
        const audio = new Audio(src);
        audio.addEventListener('ended', () => resolve(), { once: true });
        audio.addEventListener('error', () => resolve(), { once: true }); // skip if file not found
        audio.play().catch(() => resolve());
    });
}

async function playQueueSound(message) {
    // 1. Opening sound (opsional - skip jika file tidak ada)
    await playSoundFile(SOUNDS_BASE + '/opening.mp3');

    // 2. Text-to-speech - prioritaskan suara perempuan Bahasa Indonesia
    const voices = await getVoices();
    const idVoices = voices.filter(v => v.lang === 'id-ID' || v.lang.startsWith('id'));
    const isFemale = v => /female|wanita|woman|perempuan|google.*indonesia.*female/i.test(v.name || '');
    const voiceId = idVoices.find(v => isFemale(v)) || idVoices[idVoices.length - 1] || null;

    await new Promise((resolve) => {
        let resolved = false;
        const doResolve = () => {
            if (resolved) return;
            resolved = true;
            resolve();
        };

        const speech = new SpeechSynthesisUtterance(message);
        speech.lang = 'id-ID';  // Wajib: Bahasa Indonesia
        speech.rate = 0.85;
        if (voiceId) speech.voice = voiceId;

        speech.onend = () => {
            playSoundFile(SOUNDS_BASE + '/closing.mp3').then(doResolve);
        };

        window.speechSynthesis.speak(speech);
        setTimeout(doResolve, 20000); // fallback max 20 detik
    });
}

function getVoices() {
    return new Promise((resolve) => {
        const check = () => {
            const voices = window.speechSynthesis.getVoices();
            if (voices.length) {
                resolve(voices);
                return true;
            }
            return false;
        };
        if (check()) return;
        if (window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = () => { check() && (window.speechSynthesis.onvoiceschanged = null); resolve(window.speechSynthesis.getVoices()); };
        }
        let attempts = 0;
        const id = setInterval(() => {
            if (check() || ++attempts > 50) {
                clearInterval(id);
                resolve(window.speechSynthesis.getVoices());
            }
        }, 100);
    });
}

function initQueueSound() {
    if (typeof Livewire === 'undefined' || initQueueSound.done) return;
    initQueueSound.done = true;
    Livewire.on('queue-called', (message) => {
        if (message) {
            soundQueue.push(message);
            processQueue();
        }
    });
}

document.addEventListener('livewire:initialized', initQueueSound);
// Backup: jika Livewire sudah siap saat script load
if (typeof Livewire !== 'undefined') initQueueSound();
