<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class GoogleVisionService
{
    public function detectObjects($imagePath)
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . env('GOOGLE_APPLICATION_CREDENTIALS'));

        $client = new ImageAnnotatorClient();

        $image = file_get_contents($imagePath);
        $response = $client->labelDetection($image);
        $labels = $response->getLabelAnnotations();

        $objects = [];
        foreach ($labels as $label) {
            $objects[] = $label->getDescription();
        }

        $client->close();

        return $objects;
    }
}