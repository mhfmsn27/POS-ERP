// errorHandlerMixin.js

export const errorHandlerMixin = {
  methods: {
    $handleErrorResponse(error) {
      
      if (error.response != undefined) {
        if (error.response.status == 419 || error.response.status == 422 || error.response.status == 401  || error.response.status == 409) {
          this.$toast.add({
            severity: "error",
            summary: "Peringatan",
            detail: error.response.data.message,
            life: 3000,
          }); 
        } else if (error.response.status == 404) {
          this.$toast.add({
            severity: "error",
            summary: "Peringatan",
            detail: "Data yang di minta tidak dapat di temukan",
            life: 3000,
          });
        } else if (error.response.status == 403) {
          this.$toast.add({
            severity: "error",
            summary: "Peringatan",
            detail: error.response.data.message,
            life: 3000,
          });
        } else {
          this.$toast.add({
            severity: "error",
            summary: "Peringatan",
            detail:
              "Terjadi kesalahan Sistem, silahkan hubungi pihak developer atau customer service",
            life: 3000,
          });
        }
      } else {
        this.$toast.add({
          severity: "error",
          summary: "Peringatan",
          detail:
            "Terjadi kesalahan Sistem, silahkan hubungi pihak developer atau customer service",
          life: 3000,
        });
      }
    },
    $handleSuccessResponse(message) {
      this.$toast.add({
        severity: "success",
        summary: "Berhasil",
        detail: message,
        life: 3000,
      });
    },
    $goTo(payload) {
      window.parent.postMessage({
        action: "newTab",
        data: payload,
      });
    },
  },
};
