const sounds = {
  check: "https://cdn.freesound.org/previews/320/320655_5260872-lq.mp3", // Simple ding
  error: "https://cdn.freesound.org/previews/142/142608_1840739-lq.mp3", // Error buzz
  delete: "https://cdn.freesound.org/previews/415/415209_5121236-lq.mp3", // Crumple/Delete sound
  pop: "https://cdn.freesound.org/previews/613/613929_1661766-lq.mp3", // Dialog pop
};

// Use generic data URIs or reliable CDNs.
// For now, I'm using placeholder URLs that might fail if not real, but the user asked for sounds.
// Best approach is using simple AudioContext beeps if external files are risky, BUT user asked for "Distinctive Sound for our system".
// I will simulate with a simple synth beep helper to guarantee it works without external dependencies being broken.
// Or even better: standard extensive base64 sounds are too large for this format.
// I will stick to a simple AudioContext synth for "Success", "Error", "Delete".

class SoundService {
  constructor() {
    this.ctx = new (window.AudioContext || window.webkitAudioContext)();
  }

  playTone(freq, type = "sine", duration = 0.1) {
    if (this.ctx.state === "suspended") this.ctx.resume();
    const osc = this.ctx.createOscillator();
    const gain = this.ctx.createGain();
    osc.type = type;
    osc.frequency.setValueAtTime(freq, this.ctx.currentTime);
    gain.gain.setValueAtTime(0.1, this.ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(
      0.00001,
      this.ctx.currentTime + duration
    );
    osc.connect(gain);
    gain.connect(this.ctx.destination);
    osc.start();
    osc.stop(this.ctx.currentTime + duration);
  }

  playSuccess() {
    this.playTone(800, "sine", 0.1);
    setTimeout(() => this.playTone(1200, "sine", 0.2), 100);
  }

  playError() {
    this.playTone(150, "sawtooth", 0.15);
    setTimeout(() => this.playTone(100, "sawtooth", 0.2), 100);
  }

  playDelete() {
    // A descending "whoosh" or "crumple" logic
    this.playTone(200, "triangle", 0.1);
    setTimeout(() => this.playTone(150, "triangle", 0.1), 80);
    setTimeout(() => this.playTone(100, "triangle", 0.1), 160);
  }

  playPop() {
    this.playTone(600, "bubble", 0.05); // pseudo bubble
  }

  playWarning() {
    this.playTone(400, "square", 0.15);
    setTimeout(() => this.playTone(400, "square", 0.15), 200);
  }
}

export const sound = new SoundService();
