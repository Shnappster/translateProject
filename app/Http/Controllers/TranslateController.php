<?php

namespace App\Http\Controllers;

use App\Contracts\Services\TranslationServiceInterface;

class TranslateController extends Controller
{
    public function test(TranslationServiceInterface $translationService)
    {
        $translationService->export();
    }
}
