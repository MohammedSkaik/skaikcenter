import { defineStore } from "pinia";
import { sound } from "../utils/sound";

export const useToastStore = defineStore("toast", {
  state: () => ({
    toasts: [],
  }),
  actions: {
    add(toast) {
      const id = Date.now();
      const newToast = { id, ...toast };
      this.toasts.push(newToast);
      setTimeout(() => this.remove(id), 5000);

      // Play sound based on type
      try {
        if (toast.type === "success") sound.playSuccess();
        else if (toast.type === "error") sound.playError();
        else if (toast.type === "warning") sound.playWarning();
        else sound.playPop();
      } catch (e) {
        console.warn("Audio playback failed", e);
      }
    },
    remove(id) {
      this.toasts = this.toasts.filter((t) => t.id !== id);
    },
    success(message) {
      this.add({ type: "success", message });
    },
    error(message) {
      this.add({ type: "error", message });
    },
    warning(message) {
      this.add({ type: "warning", message });
    },
    info(message) {
      this.add({ type: "info", message });
    },
  },
});
