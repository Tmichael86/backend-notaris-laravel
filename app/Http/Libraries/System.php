<?php

/*
* +-------------------------------+
* |   Lib System by layhome12     |
* +-------------------------------+
* | see on my github : @layhome12 |
* +-------------------------------+
*/

namespace App\Http\Libraries;

use App\Models\Pendapatan;
use App\Models\Sidebar;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class System
{
    public function responseServer($statusCode, $data = [])
    {
        /*
        *   Informasi Status Code Server
        *
        *   200 => Success (Digunakan ketika sukses mengakses halaman)
        *   400 => Bad Request (Digunakan ketika validasi form tidak valid)
        *   401 => Unauthorized  (Digunakan ketika user tidak login)
        *   403 => Forbidden (Digunakan ketika token tidak valid)
        *   500 => Internal Server Error (Digunakan ketika terjadi kesalahan di sisi server)
        */

        return response()->json($data, $statusCode, ['Content-type' => 'application/json'], JSON_PRETTY_PRINT);
    }

    public static function getProfile($column, $id)
    {
        $dataUser = User::join('groups', function ($join) {
            $join->on('users.group_id', '=', 'groups.id');
        })
            ->select('users.*', 'groups.group_nama')
            ->where('users.id', $id)
            ->first();

        return @$dataUser->{$column} ?: null;
    }

    public static function getAccessibleSidebars()
    {
        $groupId = Auth::user()->group_id;
        $sidebars = Sidebar::join('sidebar_akses', function ($join) use ($groupId) {
            $join->on('sidebar_akses.sidebar_id', '=', 'sidebar.id')->where('sidebar_akses.group_id', '=', $groupId)->where('read', 1);
        })
            ->select('sidebar.*')
            ->where('sidebar.status', 1)
            ->orderBy('sidebar_parent_id', 'asc')
            ->orderBy('sidebar_index', 'asc')
            ->get()->toArray();

        $sortedSidebars = [];
        foreach ($sidebars as $key => $value) {
            if ($value['sidebar_parent_id'] == 0) {
                $sortedSidebars[$value['id']] = $value;
            } else {
                $sortedSidebars[$value['sidebar_parent_id']]['childs'][] = $value;
            }
        }

        return $sortedSidebars;
    }

    public static function removeHTMLTags($str)
    {
        return html_entity_decode(strip_tags($str));
    }

    public static function seoUrlEncode($text, $id)
    {
        $seo = preg_replace('/[^\w]+/', '-', strtolower($text));
        $seo .= '-' . self::strEncode($id);

        return $seo;
    }

    public static function seoUrlDecode($text)
    {
        $encodeId = array_reverse(explode('-', $text));

        return self::strDecode(@$encodeId[0]);
    }

    public static function strEncode($str)
    {
        $str = openssl_encrypt($str, 'AES-128-ECB', '!@layhome12NaN!?>');

        return base64_encode($str);
    }

    public static function strDecode($str)
    {
        $str = base64_decode($str);

        return openssl_decrypt($str, 'AES-128-ECB', '!@layhome12NaN!?>');
    }

    public static function getAccess($key = '', $code = null)
    {
        $groupId = Auth::user()->group_id;
        $urlRequest = Request::segments();

        $akses = Sidebar::join('sidebar_akses', function ($join) use ($groupId) {
            $join->on('sidebar_akses.sidebar_id', '=', 'sidebar.id')->where('sidebar_akses.group_id', '=', $groupId);
        })
            ->select('sidebar_akses.*')
            ->where('sidebar.status', 1)
            ->where(function ($q) use ($urlRequest, $code) {
                if ($code) {
                    $q->where('sidebar.sidebar_kode', $code);
                    return;
                }

                // !!
                foreach ($urlRequest as $url) {
                    $sidebarKode = str_replace('-', '', str_replace('-fetch', '', $url));
                    $q->orWhere('sidebar.sidebar_kode', $sidebarKode);
                }
            })
            ->where('sidebar.sidebar_route', '!=', '#')
            ->orderBy('sidebar.sidebar_parent_id', 'DESC')
            ->first();

        return $key == '' ? $akses : @$akses->{$key};
    }

    public static function crudIdentity($status, $data = [], $uid = '')
    {
        $userId = $uid != '' ?: Session::get('uid');

        switch ($status) {
            case 'create':
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $userId;
                $data['status'] = 1;
                break;
            case 'update':
                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = $userId;
                $data['status'] = 1;
                break;
            case 'delete':
                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = $userId;
                $data['status'] = 0;
                break;
            default:
                // code...
                break;
        }

        return $data;
    }

    public static function dateRangeParser(string $dateRange)
    {
        /**
         * @param  $dateRange
         *                    In  : DD/MM/YYYY - DD/MM/YYYY
         *                    Out : [YYYY-MM-DD, YYYY-MM-DD]
         */
        $exp = explode(' - ', $dateRange);

        $start = date('Y-m-d', strtotime(str_replace('/', '-', $exp[0])));
        $end = date('Y-m-d', strtotime(str_replace('/', '-', $exp[1])));

        return [$start, $end];
    }

    public static function dateParser(string $dateRange)
    {
        /**
         * @param  $dateRange
         *                    In  : DD/MM/YYYY
         *                    Out : YYYY-MM-DD
         */

        return date('Y-m-d', strtotime(str_replace('/', '-', $dateRange)));
    }

    public static function getYoutubeEmbedFromUrl($url)
    {
        // Getting youtube video id
        // Here is a sample of the URLs this regex matches: (there can be more content after the given URL that will be ignored)

        // http://youtu.be/dQw4w9WgXcQ
        // http://www.youtube.com/embed/dQw4w9WgXcQ
        // http://www.youtube.com/watch?v=dQw4w9WgXcQ
        // http://www.youtube.com/?v=dQw4w9WgXcQ
        // http://www.youtube.com/v/dQw4w9WgXcQ
        // http://www.youtube.com/e/dQw4w9WgXcQ
        // http://www.youtube.com/user/username#p/u/11/dQw4w9WgXcQ
        // http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/0/dQw4w9WgXcQ
        // http://www.youtube.com/watch?feature=player_embedded&v=dQw4w9WgXcQ
        // http://www.youtube.com/?feature=player_embedded&v=dQw4w9WgXcQ

        // It also works on the youtube-nocookie.com URL with the same above options.
        // It will also pull the ID from the URL in an embed code (both iframe and object tags)

        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);

        $id = @$match[1];
        if (! $id) {
            return $url;
        }

        return "https://www.youtube.com/embed/$id";
    }

    public static function dateIndo($time, $format = 'Y-m-d H:i:s')
    {
        Carbon::setLocale('id');
        $carbon = Carbon::parse($time);

        return $carbon->isoFormat($format);
    }

    public static function createSlug($text)
    {
        return preg_replace('/[^\w]+/', '_', strtolower($text));
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function arrayToObject($arr = [])
    {
        return json_decode(json_encode($arr));
    }

    public static function checkArrayDuplicate($arr = [])
    {
        $arr = collect($arr)->filter(function ($e) {
            return $e != null;
        })->toArray();

        $result = array_diff_assoc($arr, array_unique($arr));
        return count($result) > 0;
    }

    public static function getStoredData($data, $dv = "cookie")
    {
        // -- all getter cookie function will be here
    }

    public static function setStoredData($data, $dv = "cookie")
    {
        // -- all setter cookie function will be here
    }

    public static function removeStoredData($data, $dv = "cookie")
    {
        // -- all remove cookie function will be here
    }

    public static function getTotalTransaksiByCode($code)
    {
        $totalTagihan = Transaksi::where('no_akta', $code)
            ->where('status', 1)
            ->select([
                DB::raw('SUM(total) as total'),
                DB::raw('SUM(besaran_pajak_pihak_pertama + besaran_pajak_pihak_kedua) as pajak')
            ])
            ->groupBy('no_akta')
            ->first();

        return ($totalTagihan->total + $totalTagihan->pajak);
    }

    /**
     * Sync Laporan Pendapatan
     * 
     * @var $date
     * YYYY-MM-DD
     * 
     * @var array $data
     * ['penghasilan' => int, 'pengeluaran' => int]
     */
    // public static function syncLaporanPendapatan($date, $data)
    // {
    //     $penghasilan = (int) @$data['penghasilan'];
    //     $pengeluaran = (int) @$data['pengeluaran'];
    //     $pendapatan = $penghasilan - $pengeluaran;

    //     // -- get by date
    //     $db = Pendapatan::where('tanggal', $date)
    //         ->first();

    //     // -- insert data
    //     if (!$db) {
    //         // get last saldo value
    //         $latestValue = Pendapatan::query()
    //             ->latest('tanggal')
    //             ->first();

    //         $latestDate = @$latestValue->tanggal ?: date('Y-m-d');
    //         $lastSaldo = $date >= $latestDate ? @$latestValue->saldo : 0;

    //         // add last saldo with value insert
    //         Pendapatan::insert(
    //             self::crudIdentity('create', [
    //                 'tanggal' => $date,
    //                 'penghasilan' => $penghasilan,
    //                 'pengeluaran' => $pengeluaran,
    //                 'pendapatan' => $pendapatan,
    //                 'saldo' => (int) $lastSaldo + $pendapatan,
    //             ])
    //         );

    //         // update all data saldo in >date with saldo + insert value
    //         Pendapatan::whereDate('tanggal', '>', $date)
    //             ->update([
    //                 'saldo' => DB::raw("saldo+($pendapatan)")
    //             ]);

    //         return;
    //     }

    //     // -- update data
    //     $db->penghasilan += $penghasilan;
    //     $db->pengeluaran += $pengeluaran;
    //     $db->pendapatan += $pendapatan;
    //     $db->saldo += $pendapatan;

    //     // -- saving
    //     if (!$db->penghasilan && !$db->pengeluaran) {
    //         $db->delete();
    //     } else {
    //         $db->save();
    //     }

    //     // update all data saldo in >date with saldo + update value
    //     Pendapatan::whereDate('tanggal', '>', $date)
    //         ->update([
    //             'saldo' => DB::raw("saldo+($pendapatan)")
    //         ]);
    // }

    public static function syncLaporanPendapatan($date, $data)
    {
        $penghasilan = (int) @$data['penghasilan'];
        $pengeluaran = (int) @$data['pengeluaran'];
        $pendapatan = $penghasilan - $pengeluaran;
        $jenisPembayaranId = @$data['jenis_pembayaran_id'];

        // -- get by date and jenis_pembayaran_id
        $db = Pendapatan::where('tanggal', $date)
            ->where('jenis_pembayaran_id', $jenisPembayaranId)
            ->first();

        // -- get last saldo for the same jenis_pembayaran_id
        $latestSaldo = Pendapatan::where('jenis_pembayaran_id', $jenisPembayaranId)
            ->where('tanggal', '<=', $date)
            ->latest('tanggal')
            ->first();

        $lastSaldo = $latestSaldo ? $latestSaldo->saldo : 0;

        if (!$db) {
            // Insert data
            Pendapatan::create(
                self::crudIdentity('create', [
                    'tanggal' => $date,
                    'penghasilan' => $penghasilan,
                    'pengeluaran' => $pengeluaran,
                    'pendapatan' => $pendapatan,
                    'saldo' => $lastSaldo + $pendapatan,
                    'jenis_pembayaran_id' => $jenisPembayaranId
                ])
            );
        } else {
            // Update data
            $db->penghasilan += $penghasilan;
            $db->pengeluaran += $pengeluaran;
            $db->pendapatan += $pendapatan;
            $db->saldo = $lastSaldo + $db->pendapatan;

            $db->save();
        }

        // Recalculate saldo for future dates
        self::recalculateSaldo($date, $jenisPembayaranId);
    }

    public static function recalculateSaldo($startDate, $jenisPembayaranId)
    {
        $pendapatanList = Pendapatan::where('jenis_pembayaran_id', $jenisPembayaranId)
            ->where('tanggal', '>=', $startDate)
            ->orderBy('tanggal', 'ASC')
            ->get();

        $saldo = Pendapatan::where('jenis_pembayaran_id', $jenisPembayaranId)
            ->where('tanggal', '<', $startDate)
            ->latest('tanggal')
            ->value('saldo') ?? 0;

        foreach ($pendapatanList as $pendapatan) {
            $saldo += $pendapatan->pendapatan;
            $pendapatan->saldo = $saldo;
            $pendapatan->save();
        }
    }
}
