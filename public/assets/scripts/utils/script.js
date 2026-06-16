var urlParams = {
    url: new URL(window.location.href),
    get: function (name) {
        return this.url.searchParams.get(name);
    },
    set: function (name, value) {
        this.url.searchParams.set(name, value);
        window.history.replaceState(null, null, this.url); // or pushState
    },
    delete: function (name) {
        this.url.searchParams.delete(name);
        window.history.replaceState(null, null, this.url); // or pushState
    },
    getArray: function () {
        var obj = {};
        this.url.searchParams.forEach(function (value, name) {
            obj[name] = value;
        });
        return obj;
    },
};

function dateToYMD(date) {
    var d = date.getDate();
    var m = date.getMonth() + 1; //Month from 0 to 11
    var y = date.getFullYear();
    return "" + y + "-" + (m <= 9 ? "0" + m : m) + "-" + (d <= 9 ? "0" + d : d);
}

function dateToDMY(date) {
    var d = date.getDate();
    var m = date.getMonth() + 1; //Month from 0 to 11
    var y = date.getFullYear();
    return "" + (d <= 9 ? "0" + d : d) + "/" + (m <= 9 ? "0" + m : m) + "/" + y;
}

const getCookie = (key) => {
    const name = key + "=";
    const decode = decodeURIComponent(document.cookie); //to be careful
    const arrResult = decode.split('; ');
    let result;

    arrResult.forEach(val => {
        if (val.indexOf(name) === 0) result = val.substring(name.length);
    })

    return result;
}

$(document).ready(function () {
    $('input[alpha-numeric-only="true"]').on(
        "keyup keydown change",
        function (event) {
            this.value = this.value.replace(/[^a-zA-Z0-9]+/i, "");
        }
    );
});

$.fn.readmore = function (len = 125) {
    string = this.text();
    if (string) {
        if (string.length > len) {
            var text_view = `<span>${string.substring(
                0,
                len
            )}<a read="more" href="#" type="button">.. (Baca Selengkapnya)</a></span>`;
            var text_more = `<span class="more">${string.substring(
                len,
                string.length
            )} <a read="less" href="#" type="button">(Lebih Sedikit)</a></span>`;
            var p = '<p read="less">' + text_view + text_more + "<p>";
            this.html(p);
        } else {
            return string;
        }
    }

    $('a[read="more"]', this).click(function (e) {
        e.preventDefault();
        this.closest("p[read]").setAttribute("read", "more");
    });

    $('a[read="less"]', this).click(function (e) {
        e.preventDefault();
        this.closest("p[read]").setAttribute("read", "less");
    });

    return this;
};

$.fn.counter = function (config) {
    this.each(function (a, b) {
        var count = parseInt(b.getAttribute(config.attribute));
        var interval = setInterval(function () {
            if (parseInt(b.innerText) < count) {
                b.innerText = (parseInt(b.innerText) + 1).toString();
            } else {
                clearInterval(interval);
            }
        }, 25);
    });
};

$.fn.formReset = function () {
    this.each(function (index, el) {
        el.reset();
    });
    this.find('input[type="hidden"]').val("");
};

$.fn.select2Ajax = function ({ url = "", method = "GET", data = null }) {
    let context = this;
    $.httpRequest({
        url,
        method,
        data,
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        processData: true,
        response: (res) => {
            if (res.statusCode == 200) {
                context.empty().select2({
                    data: res.data,
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                });
            }
        },
    });
};

$.fn.formValue = function () {
    let data = new Object();
    $(this).each(function (index, el) {
        let name = el.name;
        let value = el.value;
        data[name] = value;
    });
    return data;
};

$.httpRequest = function ({
    url,
    method,
    data = null,
    contentType = false,
    processData = false,
    response: cb,
}) {
    $.ajax({
        url: url,
        type: method,
        headers: { 'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') },
        data: data,
        dataType: "json",
        processData: processData,
        contentType: contentType,
        async: true,
    }).always((res) => {
        typeof res["responseJSON"] === "undefined" ? cb(res) : cb(res.responseJSON);
    });
};

$.fn.formSubmit = function (callback, prefix = "") {
    this.submit(function (e) {
        e.preventDefault();

        prefix = prefix == "" ? "" : `/${prefix}`;

        let data = new FormData(this);
        let id = data.get("id") ? data.get("id") : "";

        // Unset key id
        data.delete("id");

        $("body").LoadingOverlay("show");
        $.httpRequest({
            url: id != "" ? `${this.action}/${id + prefix}` : this.action + prefix,
            method: this.method,
            data: data,
            response: (res) => {
                $("body").LoadingOverlay("hide");
                $(".message-error").empty();

                switch (res.statusCode) {
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

                // ==> Callback
                callback(res);
            },
        });
    });
};
$.fn.sl2HTML = function (attrs = {}) {
    var el = this.clone();
    el.find("select").addClass("select2");
    el.find("select").attr(attrs);
    return el.html();
};
$.fn.select2AjaxNew = function ({
    url = "",
    method = "GET",
    data = null,
    limit = 0,
}) {
    let context = this;
    $.httpRequest({
        url,
        method,
        data,
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        processData: true,
        response: (res) => {
            if (res.status === 'success' || res.statusCode == 200) {
                context.empty().select2({

                    data: res.data,
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                    dropdownAutoWidth: true,
                    width: "auto",
                    minimumInputLength: limit,
                });
            }
        },
    }, 'json');
};
