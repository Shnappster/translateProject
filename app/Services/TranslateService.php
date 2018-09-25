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
        // $this->createJsonFile(); <!-- For create JSON file -->
        // retrieve all data and save to html
        $this->getJsonScheme()->each(function ($scheme) {
            if (count($scheme->keys) === 2) {
                $data = DB::table($scheme->table)->select($scheme->keys)->get();
                $this->generateTranslationFile($data, $scheme->table, true);
            } else {
                DB::table($scheme->table)->select($scheme->keys)->get()->each(function ($item) use ($scheme) {
                    $this->generateTranslationFile($item, $scheme->table);
                });
            }
        });
    }

    public function import()
    {
        $folder = self::IMPORT_FOLDER;
        $directories = Storage::allDirectories($folder);
        foreach ($directories as $directory) {
            foreach (Storage::files($directory) as $file) {
                $table_name = explode('/', explode('public/translate/import/', $file)[1])[0];
                $parsed = $this->parseTranslateFile($file);
                if ($this->isSmallTranslateFile($file)) {
                    foreach ($parsed as $key => $value) {
                        $id = explode(':', $key)[1];

                        $arr[$id] = ['name' => $value];
                        if ($arr[$id]) {
                            $arr[$id]['name'] = $value;
                        }
                        foreach ($arr as $name => $value) {
                            DB::table($table_name)
                                ->where('id', $id)
                                ->update($value);
                        }
                    }
                } else {
                    $id = explode('(', explode('_', explode('public/translate/import/', $file)[1])[1])[0];

                    DB::table($table_name)
                        ->where('id', $id)
                        ->update($parsed);
                }
            }
        }
    }

    private function isSmallTranslateFile($file)
    {
        $arr = explode('/', $file);
        $file_name = $arr[count($arr) - 1];
        $arr2 = explode('_', $file_name);
        $after_symbol = $arr2[count($arr2) - 1];
        $is_number = explode('(', $after_symbol)[0];
        return !is_numeric($is_number);
    }

    private function parseTranslateFile($file)
    {
        if (empty($file)) {
            return null;
        } else {
            $contents = file_get_contents(storage_path('app/' . $file));
        }

        $data = explode('<!-- field:', $contents);

        $parsed = [];
        foreach ($data as $item) {
            if (!$item) {
                continue;
            }

            list($key, $value) = explode('-->', $item);

            // trim spaces
            $key = trim($key);
            $value = trim($value);
            $value = substr($value, 5, -6);

            if ($key && $value) {
                $parsed[$key] = $value;
            }
        }
        return $parsed;
    }


    private function getJsonScheme()
    {
        $filename = self::JSON_FOLDER . '/' . 'all_data' . '.json';
        $data = file_get_contents(storage_path('app/' . $filename));
        $json = json_decode($data);
        return collect($json);
    }

    private function generateTranslationFile($item, $table, $is_small = false)
    {
        $data = collect($item)->all();
        /**
         * @var string path to dir with translated data
         */
        $export_dir = self::EXPORT_FOLDER . '/' . $table;
        $import_dir = self::IMPORT_FOLDER . '/' . $table;

        // be sure what dir exists
        $this->createFolder($export_dir);
        $this->createFolder($import_dir);
        if ($is_small) {
            $small_contents = view('small_template', compact('data'))->render();
            Storage::disk('local')->put($export_dir . '/' . $table . '.html', $small_contents);
        } else {
            $large_contents = view('large_template', compact('data'))->render();
            Storage::disk('local')->put($export_dir . '/' . $table . '_' . $item->id . '.html', $large_contents);
        }
    }

    private function createFolder($dir)
    {
        if (!is_dir($dir)) {
            Storage::makeDirectory($dir);
//            dd('create folder - ' . $dir);
        }
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

    private function getDatabaseScheme($connection = null)
    {
        return collect(DB::connection($connection)->select('show tables'))->map(function ($val) {
            foreach ($val as $key => $tbl) {
                return $tbl;
            }
        });
    }
}