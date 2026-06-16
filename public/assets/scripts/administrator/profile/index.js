$("#user-img-file").change(function () {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(".foto-profile").attr("src", e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});

$(".foto-profile-card").click(function () {
    $("#user-img-file").trigger("click");
});

$("#form-pengguna").formSubmit(function (response) {
    if (response.statusCode == 200) {
        swal("Sukses !", response.message, "success");
    }
});
