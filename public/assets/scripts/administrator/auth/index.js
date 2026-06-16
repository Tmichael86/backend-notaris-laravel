$("#form").submit(function (e) {
  e.preventDefault();
  let data = new FormData(this);

  if (grecaptcha.getResponse() == "") {
    swal("Maaf !", "Harap centang reCaptha terlebih dahulu", "error");
    return;
  }

  $("body").LoadingOverlay("show");
  $.httpRequest({
    url: this.action,
    method: this.method,
    data: data,
    response: (res) => {
      $("body").LoadingOverlay("hide");
      $(".message-error").empty();
      grecaptcha.reset();

      switch (res.statusCode) {
        case 200:
          window.location.replace(base_url("/dashboard"));
          break;
        case 400:
          let error = res.data.error;
          let index = Object.keys(error);
          index.forEach((val) => {
            $(`small[data-target="${val}_error"]`).text(error[val]);
          });
          break;
        case 403:
          swal("Maaf !", res.message, "error");
          break;
        case 404:
          swal("Maaf !", res.message, "error");
          break;
        case 500:
          swal("Maaf !", res.message, "error");
          break;
        default:
          // code
          break;
      }
    },
  });
});
