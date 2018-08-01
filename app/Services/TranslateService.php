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
//         $this->createJsonFile();
        // retrieve all data and save to html
        $this->getJsonScheme()->each(function ($scheme) {
            DB::table($scheme->table)->select($scheme->keys)->get()->each(function ($item) use ($scheme) {
                $this->generateTranslationFile($item, $scheme->table);
            });
        });
    }

    private function createJsonFile()
    {
        $all_columns = $this->getDatabaseScheme()->map(function ($table) {
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
        $this->createFolder(self::JSON_FOLDER);
        $filename = self::JSON_FOLDER . '/' . 'all_data' . '.json';
        Storage::disk('local')->put($filename, $all_columns);
    }

    private function getJsonScheme()
    {
        $filename = self::JSON_FOLDER . '/' . 'all_data' . '.json';
        $data = file_get_contents(storage_path('app/' . $filename));
        $json = json_decode($data);
        return collect($json);
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