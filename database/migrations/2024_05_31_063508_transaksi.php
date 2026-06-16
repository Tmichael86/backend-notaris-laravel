<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('no_akta');
            $table->date('tanggal_daftar');
            $table->date('tanggal_selesai');
            $table->date('jatuh_tempo');
            $table->text('keterangan')->nullable();

            // => Biaya
            $table->unsignedBigInteger('biaya_layanan');
            $table->unsignedBigInteger('biaya_lainnya');
            $table->integer('petugas_id');
            $table->string('sub_total');
            $table->string('potongan_biaya');
            $table->unsignedBigInteger('total');

            // Kolom Hitung Pajak
            $table->unsignedInteger('acuan_hitung_pajak')->nullable();
            $table->unsignedInteger('nilai_pengurang')->nullable();
            $table->unsignedInteger('besaran_pajak_pihak_pertama')->nullable();
            $table->unsignedInteger('besaran_pajak_pihak_kedua')->nullable();

            // => Besaran Tidak Kena Pajak
            $table->unsignedBigInteger(column: 'besaran_nilai_tidak_kena_pajak')->nullable();
            $table->unsignedBigInteger('besaran_tidak_kena_pajak')->nullable();
            $table->unsignedBigInteger('nilai_pajak_jika')->nullable();
            $table->unsignedBigInteger('pengecekan')->nullable();
            $table->unsignedBigInteger('surat_kuasa_membebankan_hak_tanggungan')->nullable();
            $table->unsignedBigInteger('ploting_validasi')->nullable();


            // Foreign Key
            $table->integer('pemohon_id');
            $table->integer('jenis_pekerjaan_id');
            $table->integer('pekerjaan_id');
            $table->integer('kategori_pekerjaan_id');
            $table->integer('status_id');
            $table->foreignId('jenis_pajak_id')->nullable();
            $table->integer('jenis_pembayaran_id');

            // => Cetak
            $table->text('uraian_cetak_pemohon')->nullable();
            $table->text('keperluan_cetak_pemohon')->nullable();

            // => Form Tambahan Notaris
            $table->string('judul')->nullable();
            $table->string('nomor_akta')->unique()->nullable();
            $table->date('tanggal_akta')->nullable();

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->smallInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};
