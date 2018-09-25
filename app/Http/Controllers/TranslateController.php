<?php

namespace App\Http\Controllers;

use App\Contracts\Services\TranslationServiceInterface;

class TranslateController extends Controller
{
    public function main()
    {
        return view('main');
    }

    public function export(TranslationServiceInterface $translationService)
    {
        $translationService->export();
        return redirect('/');
    }

    public function import(TranslationServiceInterface $translationService)
    {
        $translationService->import();
        return redirect('/');
    }
}
