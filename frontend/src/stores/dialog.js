import { defineStore } from "pinia";
import { sound } from "../utils/sound";

export const useDialogStore = defineStore("dialog", {
  state: () => ({
    active: false,
    options: {
      title: "تأكيد",
      message: "هل أنت متأكد؟",
      type: "confirm", // confirm, delete, alert
      confirmText: "نعم",
      cancelText: "إلغاء",
    },
    resolvePromise: null,
    rejectPromise: null,
  }),
  actions: {
    confirm(message, title = "تأكيد الإجراء", options = {}) {
      this.active = true;
      this.options = {
        title,
        message,
        type: "confirm",
        confirmText: "نعم، متأكد",
        cancelText: "إلغاء",
        ...options,
      };

      sound.playPop();

      return new Promise((resolve, reject) => {
        this.resolvePromise = resolve;
        this.rejectPromise = reject;
      });
    },

    deleteConfirm(message, title = "حذف عنصر", item = "") {
      this.active = true;
      this.options = {
        title,
        message,
        item, // Extra sub-text for item name
        type: "delete",
        confirmText: "نعم، احذف",
        cancelText: "تراجع",
      };
      sound.playError(); // Use error/buzz sound for danger

      return new Promise((resolve, reject) => {
        this.resolvePromise = resolve;
        this.rejectPromise = reject;
      });
    },

    alert(message, title = "تنبيه") {
      this.active = true;
      this.options = {
        title,
        message,
        type: "alert",
        confirmText: "حسناً",
        cancelText: null,
      };
      sound.playWarning();

      return new Promise((resolve) => {
        this.resolvePromise = resolve;
      });
    },

    handleConfirm() {
      this.active = false;
      if (this.options.type === "delete") {
        sound.playDelete(); // Play crinkle/delete sound on confirm
      }
      if (this.resolvePromise) this.resolvePromise(true);
    },

    handleCancel() {
      this.active = false;
      if (this.resolvePromise) this.resolvePromise(false);
    },
  },
});
