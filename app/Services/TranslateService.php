<?php

namespace App\Services;

use App\Contracts\Services\TranslationServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TranslateService implements TranslationServiceInterface
{
    public const EXPORT_FOLDER = 'public/translate/export';
    public const IMPORT_FOLDER = 'public/translate/import';
    public const JSON_FOLDER = 'public/JSON';

    /**
     * Entry point of the DB export to html
     *
     * @return void
     */
    public function export()
    {
        $this->createJsonFile();
        // retrieve all data and save to html
        $this->getDatabaseScheme()->each(function ($table) {
            DB::table($table)->get()->each(function ($item) use ($table) {
                $this->generateTranslationFile($item, $table);
            });
        });
    }

    private function createJsonFile()
    {
        $test = $this->getDatabaseScheme()->map(function ($table) {
            $item = DB::table($table)->first();
            if (!$item) {
                return null;
            } else {
                $keys = array_keys(get_object_vars($item));
                return compact('table', 'keys');
            }
        })->filter(function ($it) {
            return !is_null($it);
        });
        $filename = $this->createFolder(self::JSON_FOLDER);
        $test = file_put_contents($filename . '/' . 'file.json', json_encode($test));
        dd($test);
    }

    private function generateTranslationFile($item, $table)
    {
        $data = collect($item)->filter(function ($it) {
            return is_string($it);
        })->all();

        /**
         * @var string path to dir with translated data
         */
        $dir = self::EXPORT_FOLDER . '/' . $table;

        // be sure what dir exists
        $this->createFolder($dir);

        $contents = view('template', compact('data'))->render();
        Storage::disk('local')->put($dir . '/' . $item->id . '.html', $contents);
    }

    private function createFolder($dir)
    {
        if (!is_dir($dir)) {
            Storage::makeDirectory($dir);
//            dd('create folder - ' . $dir);
        }
    }

    private function getDatabaseScheme($connection = null)
    {
        return collect(DB::connection($connection)->select('show tables'))->map(function ($val) {
            foreach ($val as $key => $tbl) {
                return $tbl;
            }
        });
    }
}