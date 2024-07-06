<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Facades\Log;

class GoogleVisionService
{
    public function detectObjects($imagePath)
    {
        Log::info('GoogleVisionService detectObjects called', ['imagePath' => $imagePath]);

        // Set the environment variable for Google credentials
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . env('GOOGLE_APPLICATION_CREDENTIALS'));

        try {
            // Check if the file exists
            if (!file_exists($imagePath)) {
                Log::error('Image file not found', ['path' => $imagePath]);
                return [];
            }

            $client = new ImageAnnotatorClient();

            // Read the image file
            $image = file_get_contents($imagePath);
            if ($image === false) {
                Log::error('Failed to read image file', ['path' => $imagePath]);
                return [];
            }

            $response = $client->labelDetection($image);
            $labels = $response->getLabelAnnotations();

            $objects = [];
            foreach ($labels as $label) {
                $objects[] = $label->getDescription();
            }

            $client->close();

            Log::info('Object detection successful', ['objects' => $objects]);

            return $objects;
        } catch (\Exception $e) {
            Log::error('Error in Google Vision API', [
                'error' => $e->getMessage(),
                'path' => $imagePath
            ]);
            return [];
        }
    }
}