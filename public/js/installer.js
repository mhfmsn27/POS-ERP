$("form#activasiLicensi").on("submit", function (e) {
    spinner.show();
    e.preventDefault();
    var formData = new FormData(this);
    setTimeout(function () {
        $.ajax({
            url: domain + domainpath + "/license-key/store-license",
            type: "POST",
            data: formData,
            success: function (data, json, errorThrown) {
                console.log(data);
                if (data.status == "error") {
                    Swal.fire(
                        {
                            title: "Warning", 
                            text: data.pesan,
                            icon: "warning",
                            width: "auto",
                            confirmButtonText: "Coba Kembali",
                            cancelButtonText: "Tutup",
                            showCancelButton: true,
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#openModal").on("click"); //this is when the form is in a modal
                            }
                        }
                    );
                    spinner.hide();
                } else {
                    Swal.fire({
                        title: "Berhasil",
                        text: "Selamat, Aktivasi Licensi produk anda telah berhasil, click ok untuk mulai ke halaman login aplikasi",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ok",
                    }).then((result) => {
                        if (result.value) {
                            document.location.href = domain + "/" + domainpath;
                        }
                    });
                }
            },

            cache: false,
            contentType: false,
            processData: false,
        });
    }, 130);
});

$("form#updateLicense").on("submit", function (e) {
    spinner.show();
    e.preventDefault();
    var formData = new FormData(this);
    setTimeout(function () {
        $.ajax({
            url: domain + domainpath + "/poshub-license/store-update",
            type: "POST",
            data: formData,
            success: function (data, json, errorThrown) {
                console.log(data);
                if (data.status == "error") {
                    Swal.fire(
                        {
                            title: "Warning", 
                            text: data.pesan,
                            icon: "warning",
                            width: "auto",
                            confirmButtonText: "Coba Kembali",
                            cancelButtonText: "Tutup",
                            showCancelButton: true,
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                $("#openModal").on("click"); //this is when the form is in a modal
                            }
                        }
                    );
                    spinner.hide();
                } else {
                    Swal.fire({
                        title: "Berhasil",
                        text: "Selamat, Aktivasi Licensi produk anda telah berhasil, click ok untuk mulai ke halaman login aplikasi",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ok",
                    }).then((result) => {
                        if (result.value) {
                            document.location.href = domain + "/" + domainpath;
                        }
                    });
                }
            },

            cache: false,
            contentType: false,
            processData: false,
        });
    }, 130);
});
