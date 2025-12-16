/**
 * Composant pour enregistrer la biographie en texte ou en audio
 */
class BioRecorder {
    constructor() {
        console.log('🎤 BioRecorder: Initialisation...');
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.audioBlob = null;
        this.bioType = 'text'; // 'text' ou 'audio'
        
        this.initElements();
        this.initEventListeners();
        console.log('✅ BioRecorder: Initialisé avec succès');
    }

    initElements() {
        console.log('🔍 Recherche des éléments DOM...');
        this.bioTypeRadios = document.querySelectorAll('input[name="bio_type"]');
        this.textBioContainer = document.getElementById('text-bio-container');
        this.audioBioContainer = document.getElementById('audio-bio-container');
        this.bioTextarea = document.getElementById('bio');
        this.recordBtn = document.getElementById('record-btn');
        this.stopBtn = document.getElementById('stop-btn');
        this.playBtn = document.getElementById('play-btn');
        this.deleteBtn = document.getElementById('delete-audio-btn');
        this.recordingStatus = document.getElementById('recording-status');
        this.audioPlayer = document.getElementById('audio-player');
        this.audioFileInput = document.getElementById('bio_audio_file');
        
        console.log('📋 Éléments trouvés:', {
            radios: this.bioTypeRadios.length,
            recordBtn: !!this.recordBtn,
            stopBtn: !!this.stopBtn,
            playBtn: !!this.playBtn
        });
    }

    initEventListeners() {
        // Changement de type de biographie
        this.bioTypeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => this.handleBioTypeChange(e));
        });

        // Boutons d'enregistrement
        if (this.recordBtn) {
            this.recordBtn.addEventListener('click', () => this.startRecording());
        }
        if (this.stopBtn) {
            this.stopBtn.addEventListener('click', () => this.stopRecording());
        }
        if (this.playBtn) {
            this.playBtn.addEventListener('click', () => this.playAudio());
        }
        if (this.deleteBtn) {
            this.deleteBtn.addEventListener('click', () => this.deleteAudio());
        }
    }

    handleBioTypeChange(e) {
        this.bioType = e.target.value;
        
        if (this.bioType === 'text') {
            this.textBioContainer.style.display = 'block';
            this.audioBioContainer.style.display = 'none';
            this.bioTextarea.required = true;
        } else {
            this.textBioContainer.style.display = 'none';
            this.audioBioContainer.style.display = 'block';
            this.bioTextarea.required = false;
        }
    }

    async startRecording() {
        console.log('🎤 Début de l\'enregistrement...');
        try {
            console.log('🔍 Demande d\'accès au microphone...');
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            console.log('✅ Accès au microphone accordé');
            this.mediaRecorder = new MediaRecorder(stream);
            this.audioChunks = [];

            this.mediaRecorder.ondataavailable = (event) => {
                this.audioChunks.push(event.data);
            };

            this.mediaRecorder.onstop = () => {
                this.audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                const audioUrl = URL.createObjectURL(this.audioBlob);
                this.audioPlayer.src = audioUrl;
                this.audioPlayer.style.display = 'block';
                this.playBtn.disabled = false;
                this.deleteBtn.disabled = false;
                
                // Convertir en File pour l'upload
                const audioFile = new File([this.audioBlob], 'bio-audio.webm', { type: 'audio/webm' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(audioFile);
                this.audioFileInput.files = dataTransfer.files;
            };

            this.mediaRecorder.start();
            this.isRecording = true;
            this.updateRecordingUI();
            this.recordingStatus.textContent = 'Enregistrement en cours...';
            this.recordingStatus.style.color = '#e74c3c';
        } catch (error) {
            console.error('Erreur lors de l\'accès au microphone:', error);
            alert('Impossible d\'accéder au microphone. Veuillez autoriser l\'accès.');
        }
    }

    stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            this.isRecording = false;
            this.updateRecordingUI();
            this.recordingStatus.textContent = 'Enregistrement terminé';
            this.recordingStatus.style.color = '#27ae60';
        }
    }

    playAudio() {
        if (this.audioPlayer.src) {
            this.audioPlayer.play();
        }
    }

    deleteAudio() {
        this.audioBlob = null;
        this.audioChunks = [];
        this.audioPlayer.src = '';
        this.audioPlayer.style.display = 'none';
        this.audioFileInput.value = '';
        this.playBtn.disabled = true;
        this.deleteBtn.disabled = true;
        this.recordingStatus.textContent = '';
    }

    updateRecordingUI() {
        if (this.isRecording) {
            this.recordBtn.disabled = true;
            this.stopBtn.disabled = false;
            this.recordBtn.style.opacity = '0.5';
            this.stopBtn.style.opacity = '1';
        } else {
            this.recordBtn.disabled = false;
            this.stopBtn.disabled = true;
            this.recordBtn.style.opacity = '1';
            this.stopBtn.style.opacity = '0.5';
        }
    }
}

// Initialiser le composant au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    console.log('📄 DOM chargé');
    const bioForm = document.getElementById('bio-form');
    if (bioForm) {
        console.log('✅ Formulaire bio trouvé, initialisation du BioRecorder...');
        new BioRecorder();
    } else {
        console.warn('⚠️ Formulaire bio non trouvé (id="bio-form")');
    }
});
