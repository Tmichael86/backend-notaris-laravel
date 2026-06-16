<?php

namespace Database\Seeders;

use App\Http\Libraries\System;
use App\Models\HargaPekerjaanPPAT;
use App\Models\PekerjaanPPAT;
use App\Models\ProsesPekerjaanPPAT;
use Illuminate\Database\Seeder;

class PekerjaanPPATSeeder extends Seeder
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
                "nama" => "Balik Nama Sertifikat",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 4,
                        "harga" => "5000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 6,
                        "harga" => "5000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 5,
                        "harga" => "4000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 7,
                        "harga" => "5000000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 17,
                        "harga" => "0",
                        "estimasi_waktu" => "2 bulan dari Berkas Masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 18,
                        "harga" => "2000000",
                        "estimasi_waktu" => "2 bulan dari Berkas Masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 20,
                        "harga" => "35000000",
                        "estimasi_waktu" => "1 tahun setelah Berkas masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 19,
                        "harga" => "5000000",
                        "estimasi_waktu" => "6 bulan dari Berkas Masuk BPN"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Berkas Peralihan Masuk Ke BPN dan Pembayaran SPS",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendalaman dan list kelengkapan berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pengecekan, Ploting dan Validasi Sertifikat",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan dan Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendaftaran dan Pembayaran Pajak",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyiapan Dokumen Peralihan Hak",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Sertifikat yang telah selesai",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pemecahan Sertifikat",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 8,
                        "harga" => "9000000",
                        "estimasi_waktu" => "6 bulan setelah berkas masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 9,
                        "harga" => "13500000",
                        "estimasi_waktu" => "6 bulan setelah berkas masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 10,
                        "harga" => "18000000",
                        "estimasi_waktu" => "6 bulan setelah berkas masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 11,
                        "harga" => "22500000",
                        "estimasi_waktu" => "6 bulan setelah berkas masuk BPN"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Survey Lokasi",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pengecekan, Ploting dan Validasi",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pengukuran",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Berkas Pemecahan Masuk BPN dan Pembayaran SPS",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan SHM Jadi",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pembetulan Sertifikat",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 12,
                        "harga" => "5000000",
                        "estimasi_waktu" => "1 tahun setelah Berkas masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 13,
                        "harga" => "700000",
                        "estimasi_waktu" => "2 bulan dari Berkas Masuk BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 14,
                        "harga" => "4000000",
                        "estimasi_waktu" => "1 Tahun dari Berkas Masuk BPN"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyiapan Berkas dan Survey Lokasi",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pengukuran Ulang Sertifikat",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Berkas Pembetulan Masuk BPN",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Sertifikat Jadi dan Penyerahan Sertifikat",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Pendaftaran Hak Pertama Kali (Konversi)",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "11000000",
                        "estimasi_waktu" => "1 - 3 Tahun  setelah berkas masuk Ke BPN"
                    ],
                    [
                        "kategori_pekerjaan_id" => 15,
                        "harga" => "12000000",
                        "estimasi_waktu" => "1 - 3 Tahun  setelah berkas masuk Ke BPN"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Survey Lokasi",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pengukuran dan Peta Bidang",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendaftaran dan Pembayaran Pajak Peralihan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan dan Penandatanganan Akta Peralihan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Berkas Pendaftaran Hak Masuk Ke BPN",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Sertifikat jadi dan Penyerahan SHM",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "APHT",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 3,
                        "harga" => "7000000",
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
                        "nama" => "Pemesanan Nama CV",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draf dan penandatanganan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Salinan dan Pendaftaran AHU",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pencetakan SK",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Hasil dan Pembayaran Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Roya",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 16,
                        "harga" => "700000",
                        "estimasi_waktu" => "1 bulan setelah berkas masuk BPN"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pendaftaran berkas dan pembayaran SPS",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Sertifikat dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ],
            [
                "nama" => "Akta PPAT",
                "list_harga" => [
                    [
                        "kategori_pekerjaan_id" => 4,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 6,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ],
                    [
                        "kategori_pekerjaan_id" => 7,
                        "harga" => "1500000",
                        "estimasi_waktu" => "4-7 Hari setelah berkas dinyatakan lengkap"
                    ]
                ],
                "list_proses" => [
                    [
                        "nama" => "Pendalaman berkas dan List Kelengkapan Berkas",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Pembuatan Draft Akta dan Akta Salinan",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penandatanganan Akta",
                        "detail" => "-"
                    ],
                    [
                        "nama" => "Penyerahan Akta dan Pelunasan",
                        "detail" => "-"
                    ]
                ]
            ]
        ];

        // each pekerjaan ppat -> harga, proses
        foreach ($data as $item) {
            $pekerjaan = PekerjaanPPAT::create(
                System::crudIdentity('create', [
                    'nama' => $item['nama'],
                ])
            );

            foreach ($item['list_harga'] as $harga) {
                $harga['pekerjaan_ppat_id'] = $pekerjaan->id;

                HargaPekerjaanPPAT::create(
                    System::crudIdentity('create', $harga)
                );
            }

            foreach ($item['list_proses'] as $proses) {
                $proses['pekerjaan_ppat_id'] = $pekerjaan->id;

                ProsesPekerjaanPPAT::create(
                    System::crudIdentity('create', $proses)
                );
            }
        }
    }
}
