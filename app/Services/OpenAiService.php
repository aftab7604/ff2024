<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class OpenAiService
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

    public function generateResponse($messages)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'api-key' => $this->apiKey,
        ])
        ->timeout(60) // Set request timeout to 60 seconds
        ->retry(3, 2000) // Retry 3 times, with a 2-second delay between attempts
        ->withoutVerifying() // Disable SSL certificate verification
        ->post($this->endpoint, [
            'messages' => $messages,
            'max_tokens' => 4096, // Set max tokens to 4096 // it was 500
            'temperature' => 0.1, // Set temperature to 0.1
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            // Handle error, maybe log or throw an exception
            throw new \Exception('Error fetching response from OpenAI.');
        }
    }

    public function extractTextFromAzureOCR($fileContent)
    {
        $response = Http::withHeader([
            'Content-Type' => 'application/pdf',
            'Ocp-Apim-Subscription-Key' => $this->apiKey_ocr
        ])
        ->withoutVerifying()
        ->post($this->endpoint_ocr, [
            'data' => $fileContent
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Error fetching response from Azure OCR: ' . $response->body());
        }
    }

    public function SendMessageToOpenAIAPI($text, $language)
    {
        if ($language == 'hebrew')
        {
            $messages = [
                ['role' => 'system', 'content' => 'Please respond in hebrew.'],
                ['role' => 'user', 'content' => $text]
            ];
        }
        else
        {
            $messages = [
                ['role' => 'user', 'content' => $text]
            ];
        }

        try {
            // Call the OpenAI service
            $response = $this->generateResponse($messages);

            // Extract and return the response
            $generatedText = $response['choices'][0]['message']['content'];

            return response()->json([
                'message' => 'Success',
                'response' => $generatedText,
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode();
            if($code == 429){
                return response()->json([
                    'message' => 'Error',
                    'error' => "Oops! It looks like you’re sending too many requests. Please wait a few seconds and try again.",
                    'code'=>$code,
                ], 500);
            }else{
                return response()->json([
                    'message' => 'Error',
                    'error' => $e->getMessage(),
                    'code'=>$code,
                ], 500);
            }
        }
    }

    public function GetPDFContentFromAzureOCR($filePath)
    {
        
        $analysisResponse = $this->analyzeDocument($filePath);
        $operationResponse = $this->getAnalysisResult($analysisResponse);

        $text = $this->extractText($operationResponse);

        return $text;
    }

    private function analyzeDocument($file)
    {
        $client = new Client();

        $headers = [
            'Content-Type' => 'application/pdf',
            'Ocp-Apim-Subscription-Key' => $this->apiKey_ocr
        ];

        // dd($headers);

        $response = $client->post($this->endpoint_ocr, [
            'headers' => $headers,
            'verify'=>false,
            'body' => fopen($file->getRealPath(), 'r')
        ]);

        if ($response->getStatusCode() == 202) 
            return $response->getHeader('Operation-Location')[0];
        else
            return response()->json(['error' => 'Error analyzing the document!']);
    }

    private function getAnalysisResult($operationLocation)
    {
        $client = new Client();

        $headers = [
            'Ocp-Apim-Subscription-Key' => $this->apiKey_ocr
        ];

        do
        {
            sleep(3);
            $resultResponse = $client->get($operationLocation, [
                'headers' => $headers,
                'verify'=>false,
                'timeout' => 120, // Increase timeout to 120 seconds
                'read_timeout' => 120,
                'connect_timeout' => 120,
            ]);
            $result = json_decode($resultResponse->getBody(), true);
            $status = $result['status'] ?? null;
        }
        while ($status === 'running');

        if ($status === 'succeeded')
        {
            return $result;
        }
        else
        {
            return response()->json(['error' => 'Error getting result!']);
        }
    }

    private function extractText($result)
    {
        $text = '';

        foreach ($result['analyzeResult']['readResults'] as $page)
        {
            foreach ($page['lines'] as $line)
            {
                $text .= $line['text'] . "\n";
            }
        }

        return $text;
    }

    public function ChatWithOpenAIAPI($messages, $language)
    {
        $systemMessage = ['role' => 'system', 'content' => 'Please respond in hebrew.'];

        if ($language == 'hebrew')
            array_unshift($messages, $systemMessage);

        // dd($messages);

        try {
            // Call the OpenAI service
            $response = $this->generateResponse($messages);

            // Extract and return the response
            $generatedText = $response['choices'][0]['message']['content'];

            return response()->json([
                'message' => 'Success',
                'response' => $generatedText,
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode();
            if($code == 429){
                return response()->json([
                    'message' => 'Error',
                    'error' => "Oops! It looks like you’re sending too many requests. Please wait a few seconds and try again.",
                    'code'=>$code,
                ], 500);
            }else{
                return response()->json([
                    'message' => 'Error',
                    'error' => $e->getMessage(),
                    'code'=>$code,
                ], 500);
            }
        }
    }
}
