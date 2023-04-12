<?php

namespace App\Http\Controllers;

use App\Jobs\salesCsvProcess;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function create(){
        return view('upload-file');
    }

    public function store(){
        if(request()->has('mycsv')) {
            $data = file(request()->file('mycsv'));

            $path = resource_path('temp/');

            $chunks = array_chunk($data, 1000);
            foreach($chunks as $key => $chunk) {
                $name = "temp{$key}.csv";
                file_put_contents($path.$name, $chunk);
            }
            $files = glob("{$path}/temp*.csv");

            $header = [];
            $batch = Bus::batch([])->name('Import CSV')->dispatch();
            foreach($files as $key => $file){
                $data = array_map('str_getcsv', file($file));
                if($key === 0){
                    $header = $data[0];
                    unset($data[0]);
                }
                $batch->add(new salesCsvProcess($data, $header));
                unlink($file);
            }
            return $batch;
        }
        return 'please upload file';
    }

    public function batch()
    {
        $batchId = request('id');
        return Bus::findBatch($batchId);
    }

    public function batchInProgress() {
        $batch =  DB::table('job_batches')->where('pending_jobs', '>', 0)->first();
        return $batch ? Bus::findBatch($batch->id) : [];
    }
}
