<?php

namespace App\Console\Commands;

use App\Models\HariLibur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetApiNationalHoliday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:api-hari-libur';
    protected $description = 'Get Hari Libur Nasional from API https://github.com/kresnasatya/api-harilibur';

    public function handle()
    {
        $url = "https://dayoffapi.vercel.app/api";

        $curl = curl_init();

        // Set URL dan opsi lainnya
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Eksekusi cURL dan simpan respons
        $response = curl_exec($curl);

        // Tutup cURL
        curl_close($curl);

        $data = json_decode($response);
        if(!empty($data)) {
            DB::table('hari_libur')->truncate();
            foreach ($data as $val) {
                HariLibur::create([
                    'name' => $val->keterangan,
                    'date' => $val->tanggal,
                    'is_cuti' => $val->is_cuti
                ]);
            }
            $this->info('Hari Libur Nasional update successfully.');
        } else {
            $this->info('API Hari Libur Nasional tidak terdapat data');
        }
    }
}
