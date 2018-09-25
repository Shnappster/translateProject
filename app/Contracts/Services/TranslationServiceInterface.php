<?php

namespace App\Contracts\Services;

interface TranslationServiceInterface
{
    public function export();
    public function import();
}