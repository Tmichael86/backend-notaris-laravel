<?php

namespace Database\Seeders;

use App\Http\Libraries\System;
use App\Models\HargaPekerjaanNotaris;
use App\Models\PekerjaanNotaris;
use App\Models\ProsesPekerjaanNotaris;
use Illuminate\Database\Seeder;

class PekerjaanNotarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "nama" => "Perjanjian Kredit",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 3,
                        "harga" => "500000",
                        "estimasi_waktu" => "-"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "List kelengkapan berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan draf dan penandatanganan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Hasil dan Pembayaran",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pengakuan Hutang",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 3,
                        "harga" => "0",
                        "estimasi_waktu" => "-"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "List kelengkapan berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan draf dan penandatanganan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan hasil dan pembayaran Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Fidusia",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 3,
                        "harga" => "0",
                        "estimasi_waktu" => "-"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "List kelengkapan berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pebuatan Draf dan penandatanganan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan hasil dan pembayaran Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pendirian Perusahaan Terbatas (PT)",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "5000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "8000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendaftaran dan Pemesanan Nama Perseroan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan Akta dan Pendaftaran AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pengambilan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Perubahan Perusahaan Terbatas (PT)",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "5000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "6500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "List kelengkapan berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan draf dan penandatanganan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan salinan dan Pendaftaran AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan SK",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan hasil dan Pembayaran Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pendirian CV",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "2000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman dan Penelitian Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendaftaran dan Pemesanan Nama Perseroan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan Akta dan Pendaftaran SABU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pengambilan Akta Salinan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Perubahan Anggaran Dasar CV",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "2000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Akta Perubahan dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan dan Pendaftaran Perubahan di SABU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan Pendaftaran Perubahan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan Akta Perubahan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pendiriann Perkumpulan",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "1500000",
                        "estimasi_waktu" => "1 bulan setelah Nama Perkumpulan di acc"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "4000000",
                        "estimasi_waktu" => "1 bulan setelah Nama Perkumpulan di acc"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendaftaran Nama Perkumpulan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan Akta dan Pendaftaran Pengesahan AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan Pendirian dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Perubahan Anggaran Dasar Perkumpulan",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "3500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan Akta dan Pendaftaran Perubahan AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan Akta dan Pengesahan AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan Akta dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pendirian Yayasan",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "2000000",
                        "estimasi_waktu" => "1 bulan setelah Nama Yayasan di acc AHU"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "5000000",
                        "estimasi_waktu" => "1 bulan setelah Nama Yayasan di acc AHU"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendaftaran dan Pemesanan Nama Yayasan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan Akta dan Pendaftaran Pendirian Yayasan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan Pendirian Yayasan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan Akta Pendirian Yayasan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Perubahan Anggaran Dasar Yayasan",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 1,
                        "harga" => "2000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 2,
                        "harga" => "5000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta Perubahan dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan Akta dan Pendaftaran Perubahan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan Perubahan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan Akta Perubahan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Surat Kuasa Membebankan Hak Tanggungan (SKMHT)",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman dan list kelengkapan berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan dan Pencetakan Salinan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "KUASA DAN PERSETUJUAN",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "2000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan dan Pencetakann Salinan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pernyataan Hak Mewarisi",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "1000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan dan Pencetakan Salinan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Perjanjian Kerja Sama",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "2000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Penelitian Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta Dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan dan Pencetakan Salinan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pendirian Koperasi",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "4000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "6000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan Akta dan Pendaftaran Pendirian Koperasi",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan Pendirian Koperasi",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Perubahan AD Koperasi",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "2000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "3000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan dan Pendaftaran Perubahan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan Salinan dan Pengesahan Pendaftaran Perubahan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Salinan Perubahan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Warmerking",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "150000",
                        "estimasi_waktu" => "1-2 hari kerja"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "75000",
                        "estimasi_waktu" => "1-2 hari kerja"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan dan Penandatanganan Warmerking",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Fotocopy dan Pengarsipan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Legalisasi",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "300000",
                        "estimasi_waktu" => "2 hari kerja"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "500000",
                        "estimasi_waktu" => "2 hari kerja"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Legalisasi dan penandatanganan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Fotocopy dan Pengarsipan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ]
        ];

        // each pekerjaan notaris -> harga, proses
        foreach ($data as $item) {
            $pekerjaan = PekerjaanNotaris::create(
                System::crudIdentity('create', [
                    'nama' => $item['nama'],
                ])
            );

            foreach ($item['list_harga'] as $harga) {
                $harga['pekerjaan_notaris_id'] = $pekerjaan->id;

                HargaPekerjaanNotaris::create(
                    System::crudIdentity('create', $harga)
                );
            }

            foreach ($item['list_proses'] as $proses) {
                $proses['pekerjaan_notaris_id'] = $pekerjaan->id;

                ProsesPekerjaanNotaris::create(
                    System::crudIdentity('create', $proses)
                );
            }
        }
    }
}
