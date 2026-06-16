let atributValue = 0;

let table_harga = $(".table-harga").DataTable({
    autoWidth: false,
    responsive: true,
    ordering: false,
    searching: false,
    info: false,
    sorting: false,
    paging: false,
});

let table_proses = $(".table-proses").DataTable({
    autoWidth: false,
    responsive: true,
    ordering: false,
    searching: false,
    info: false,
    sorting: false,
    paging: false,
});

let table = $(".table-pekerjaan-notaris").DataTable({
    ajax: {
        url: baseUrl("/master/pekerjaan_notaris-fetch"),
        headers: {
            "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
        },
        dataSrc: "data",
        type: "POST",
    },
    processing: true,
    serverSide: true,
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    columns: [
        {
            name: "",
            render: function (data, i, row, meta) {
                return meta.row + 1;
            },
            width: "20px",
        },
        {
            data: "nama",
        },
        {
            data: "harga",
        },
        {
            data: "estimasi_waktu",
        },
        {
            data: "created_at",
            render: function (data, i, row) {
                return new Date(data).toLocaleString();
            },
        },
        {
            data: "id",
            render: function (data, i, row) {
                var div = document.createElement("div");
                div.className = "row-action";

                var btnPreview = document.createElement("button");
                btnPreview.className = "btn btn-info btn-action action-preview";
                btnPreview.innerHTML = '<i class="fa fa-location-arrow"></i>';
                if (row.template_nama != null) div.append(btnPreview);

                var btnEdit = document.createElement("button");
                btnEdit.className = "btn btn-warning btn-action action-edit";
                btnEdit.innerHTML = '<i class="fa fa-edit"></i>';
                if (access.update == 1) div.append(btnEdit);

                var btnDelete = document.createElement("button");
                btnDelete.className = "btn btn-danger btn-action action-hapus";
                btnDelete.innerHTML = '<i class="fas fa-eraser"></i>';
                if (access.delete == 1) div.append(btnDelete);

                return div.outerHTML;
            },
            width: "180px",
        },
    ],
    createdRow: function (row, data) {
        $(".action-edit", row).click(function (e) {
            e.preventDefault();
            table_harga.clear().draw();
            table_proses.clear().draw();

            $.httpRequest(
                {
                    url: baseUrl("/master/pekerjaan_notaris/" + data.id),
                    method: "GET",
                    contentType:
                        "application/x-www-form-urlencoded; charset=UTF-8",
                    processData: true,
                    response: function (res) {
                        let datas = res;

                        $('input[name="id"]').val(data.id);
                        $('input[name="nama"]').val(datas.nama);

                        $.each(datas.harga, function (index, val) {
                            table_harga.row
                                .add([
                                    $("#clone_aksi_harga").html(),
                                    $("#clone_kategori_pekerjaan_id").sl2HTML({
                                        target: "kategori_pekerjaan_id",
                                    }),
                                    $("#clone_pekerjaan_harga").html(),
                                    $("#clone_estimasi_waktu").html(),
                                ])
                                .draw(true);

                            $('select[target="kategori_pekerjaan_id"]')
                                .last()
                                .select2AjaxNew({
                                    url: baseUrl(
                                        "/master/pekerjaan_notaris/getkategori"
                                    ),
                                    method: "POST",
                                    data: {
                                        selected: val.kategori_pekerjaan_id,
                                    },
                                });

                            $('tbody input[name="harga[pekerjaan_harga_id][]"]')
                                .last()
                                .val(val.id);

                            $('tbody input[name="harga[pekerjaan_harga][]"]')
                                .last()
                                .val(formatRupiah(val.harga));

                            $('tbody input[name="harga[estimasi_waktu][]"]')
                                .last()
                                .val(val.estimasi_waktu);
                        });

                        $.each(datas.proses, function (index, val) {
                            table_proses.row.add([
                                $("#clone_aksi_proses").html(),
                                $("#clone_pekerjaan_proses").html()
                            ]).draw(false);

                            let row = table_proses.row(':last').node();
                            let $row = $(row);

                            $row.find('input[name="proses_id[]"]').val(val.id);
                            $row.find('input[name="proses[]"]').val(val.nama);
                            $row.find('textarea[name="proses_detail[]"]').val(val.detail);
                            $row.find('.atribut_value').val(index);

                            $.each(val.atribut, function (attrIndex, attrVal) {
                                let newAttribute = $("#clone_proses_pekerjaan_atribut")
                                    .clone()
                                    .removeAttr("id")
                                    .removeClass("d-none")
                                    .show();

                                newAttribute
                                    .find(".atribut_list_id")
                                    .attr("name", `atribut[${index}][proses_pekerjaan_atribut_id][]`)
                                    .val(attrVal.id);

                                newAttribute
                                    .find(".atribut_list")
                                    .attr("name", `atribut[${index}][proses_pekerjaan_atribut][]`)
                                    .val(attrVal.atribut);

                                $row.find('.proses-atribut-container').append(newAttribute);
                            });
                        });

                        // attribute setter
                        atributValue = datas.proses.length;

                        $(".message-error").empty();

                        initializeOnChange();
                        $("#modal-form").modal("show");
                    },
                },
                "json"
            );
        });

        $(".action-hapus", row).click(function (e) {
            e.preventDefault();
            swal({
                title: "Peringatan!",
                text: "Anda yakin akan menghapus data ini?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#993333",
                cancelButtonColor: "#5d5d5d",
                cancelButtonText: "Tidak",
                confirmButtonText: "Hapus",
            }).then(
                function () {
                    $.LoadingOverlay("show");
                    $.httpRequest({
                        url: baseUrl(`/master/pekerjaan_notaris/${data.id}`),
                        method: "DELETE",
                        response: (res) => {
                            $.LoadingOverlay("hide");
                            if (res.statusCode == 200) {
                                swal("Sukses!", res.message, "success");
                                table.ajax.reload();
                            }
                        },
                    });
                },
                function (dismiss) {
                    if (dismiss === "cancel") {
                    }
                }
            );
        });
    },
});

$(".datepicker").datepicker();

$("#btn-form-add").click(function () {
    table_harga.clear().draw();
    table_proses.clear().draw();
    $(".btn-add-row-harga").click();
    $(".btn-add-row-proses").click();
    $("#form-pekerjaan").formReset();
    $(".message-error").empty();
    $("#modal-form").modal("show");
});

$("#form-pekerjaan").formSubmit((response) => {
    if (response.statusCode == 200) {
        $("#form-pekerjaan").formReset();
        $("#modal-form").modal("hide");

        swal("Sukses !", response.message, "success");
        table.ajax.reload();
    }
});

function removeRowHarga(el) {
    swal({
        title: "Peringatan!",
        text: "Anda akan menghapus item yang dipilih.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "Tidak",
        confirmButtonText: "Ya",
    }).then(function () {
        var row = table_harga.row($(el).closest("tr"));
        row.remove().draw();
    });
}

function removeRowProses(el) {
    swal({
        title: "Peringatan!",
        text: "Anda akan menghapus item yang dipilih.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "Tidak",
        confirmButtonText: "Ya",
    }).then(function () {
        var row = table_proses.row($(el).closest("tr"));
        row.remove().draw();
    });
}

function formatRupiah(angka) {
    var reverse = angka.toString().split("").reverse().join("");
    var ribuan = reverse.match(/\d{1,3}/g);
    var formatted = ribuan.join(".").split("").reverse().join("");
    return formatted;
}

function initializeOnChange() {
    $('[target="pekerjaan_harga"]').on("input", function () {
        const eIndex = $('[target = "pekerjaan_harga"]').index(this);
        const value = $('[target="pekerjaan_harga"]').eq(eIndex).val();
        var numericVal = value.replace(/[^0-9]/g, "");
        var formattedVal = formatRupiah(numericVal);
        $('[target="pekerjaan_harga"]').eq(eIndex).val(formattedVal);
    });
}

$(".btn-add-row-harga").click(function () {
    table_harga.row
        .add([
            $("#clone_aksi_harga").html(),
            $("#clone_kategori_pekerjaan_id").sl2HTML({
                target: "kategori_pekerjaan_id",
            }),
            $("#clone_pekerjaan_harga").html(),
            $("#clone_estimasi_waktu").html(),
        ])
        .draw(false);

    $('select[target="kategori_pekerjaan_id"]')
        .last()
        .select2AjaxNew({
            url: baseUrl("/master/pekerjaan_notaris/getkategori"),
            method: "POST",
        });
    initializeOnChange();
});

$(".btn-add-row-proses").click(function () {
    let dataAtribut = $("#clone_pekerjaan_proses").clone();
    dataAtribut.find(".atribut_value").attr("value", atributValue);

    atributValue += 1;
    dataAtribut = dataAtribut.removeAttr("id").html();

    table_proses.row
        .add([
            $("#clone_aksi_proses").clone().removeAttr("id").html(),
            dataAtribut,
        ])
        .draw(false);

    initializeOnChange();
});

$(document).on("click", ".btn-add-atribute", function () {
    let atributValue = $(this).closest(".pt-2").find(".atribut_value").val();
    atributValue = atributValue === "" ? 0 : atributValue;

    let listAtribut = $("#clone_proses_pekerjaan_atribut").clone();

    listAtribut
        .find(".atribut_list")
        .attr("name", `atribut[${atributValue}][proses_pekerjaan_atribut][]`);

    listAtribut
        .find(".atribut_list_id")
        .attr("name", `atribut[${atributValue}][proses_pekerjaan_atribut_id][]`);

    listAtribut = listAtribut.removeAttr("id")
        .removeClass("d-none")
        .html();

    let container = $(this)
        .closest(".pt-2")
        .find(`[id^='proses-atribut-container-']`);

    container.last().append(listAtribut);
    initializeOnChange();
});

$(document).on("click", ".btn-remove-atribute", function () {
    let $row = $(this).closest(".d-flex");

    swal({
        title: "Peringatan!",
        text: "Anda akan menghapus item yang dipilih.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "Tidak",
        confirmButtonText: "Ya",
    }).then(function (result) {
        if (result == true) {
            $row.remove();
        }
    });

    initializeOnChange();
});

$("#export-to-excel").on("click", function () {
    // Mengambil data dari DataTable
    var data = table.rows({ filter: 'applied' }).data().toArray();
    var formattedData = data.map((row, index) => ({
        No: index + 1,
        Nama: row.nama,
        Harga: row.harga,
        EstimasiWaktu: row.estimasi_waktu,
        CreatedAt: new Date(row.created_at).toLocaleString()
    }));

    // Membuat workbook dan worksheet
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.json_to_sheet(formattedData);

    // Menambahkan worksheet ke workbook
    XLSX.utils.book_append_sheet(wb, ws, "Data Pekerjaan Notaris");

    // Mengunduh file Excel
    XLSX.writeFile(wb, "data_pekerjaan_notaris.xlsx");
});
