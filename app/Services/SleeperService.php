
<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class SleeperService
{
    protected $endpoint;
    protected $apiKey;
    
    protected $endpoint_ocr;
    protected $apiKey_ocr;

    public function __construct()
    {
        $this->endpoint = env('AZURE_OPENAI_ENDPOINT');
        $this->apiKey = env('AZURE_OPENAI_API_KEY');
        $this->endpoint_ocr = env('AZURE_OCR_ENDPOINT','https://pdf1966.cognitiveservices.azure.com/') . 'formrecognizer/v2.1/layout/analyze';
        $this->apiKey_ocr = env('AZURE_OCR_KEY');
    }
}