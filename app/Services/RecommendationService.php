<?php

namespace App\Services;

use Phpml\Math\Distance\Euclidean;
use Phpml\Metric\Distance;
use App\Models\Book;
use App\Models\BookInteraction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    private $distanceMetric;

    public function __construct()
    {
        $this->distanceMetric = new Euclidean();
    }

    public function generateRecommendationQuery($userId, $limit = 5)
    {
        Log::info("Generating recommendation query for user: $userId");

        $userInteractions = $this->getUserInteractions($userId);
        Log::info("User interactions:", $userInteractions);

        $allUsers = $this->getAllUsers();
        Log::info("Total users for similarity calculation: " . count($allUsers));

        $similarities = $this->calculateSimilarities($userId, $userInteractions, $allUsers);
        Log::info("User similarities:", array_slice($similarities, 0, 5, true)); // Log top 5 similar users

        $recommendedBookIds = $this->getTopRecommendations($similarities, $userInteractions, $limit);
        Log::info("Recommended book IDs:", $recommendedBookIds);

        $query = $this->constructQuery($recommendedBookIds);
        Log::info("Constructed query: $query");

        return $query;
    }

    private function constructQuery($recommendedBookIds)
    {
        if (empty($recommendedBookIds)) {
            Log::info("No recommended book IDs. Using default query.");
            return "popular books";
        }
    
        // Get the top recommended book ID (first key of the array)
        $topBookId = array_key_first($recommendedBookIds);
        
        $book = Book::find($topBookId);
        
        if (!$book) {
            Log::info("Top recommended book not found. Using default query.");
            return "popular books";
        }
    
        Log::info("Constructing query from top recommended book: {$book->title} (ID: {$book->id})");
    
        // Extract two significant words from the title
        $titleWords = $this->extractSignificantWords($book->title, 2);
        $query = implode(' ', $titleWords);
    
        // If we couldn't get two words from the title, add the author's last name
        if (count($titleWords) < 2 && $book->author) {
            $authorLastName = $this->getAuthorLastName($book->author);
            $query .= ' ' . $authorLastName;
        }
    
        $query = '"' . trim($query) . '"';
        
        Log::info("Final constructed query: $query");
    
        return $query;
    }
    
    private function extractSignificantWords($title, $wordCount)
    {
        // Remove common words that might not be significant
        $commonWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with'];
        $words = str_word_count(strtolower($title), 1); // Get an array of words
        $words = array_diff($words, $commonWords);
        
        // If we have fewer words than requested after removing common words, use all available words
        if (count($words) <= $wordCount) {
            return $words;
        }
        
        // Otherwise, return the first $wordCount words
        return array_slice($words, 0, $wordCount);
    }
    
    private function getAuthorLastName($author)
    {
        $nameParts = explode(' ', trim($author));
        return end($nameParts);
    }
    ///////////// query containing all the tops books
    // private function constructQuery($recommendedBookIds)
    // {
    //     $books = Book::whereIn('id', array_keys($recommendedBookIds))->get();
        
    //     Log::info("Constructing query from books:", $books->pluck('title', 'id')->toArray());
    
    //     if ($books->isEmpty()) {
    //         Log::info("No books found for recommended IDs. Using default query.");
    //         return "popular books";
    //     }
    
    //     $queryParts = [];
    //     foreach ($books as $book) {
    //         $queryParts[] = $book->title;
    //         $queryParts[] = $book->author;
    //     }
        
    //     $queryParts = array_unique($queryParts);
    //     $query = implode(' OR ', array_map(function($part) {
    //         return '"' . trim($part) . '"';
    //     }, $queryParts));
        
    //     if (empty($query)) {
    //         Log::info("Generated query is empty. Using default query.");
    //         return "popular books";
    //     }
    
    //     Log::info("Final constructed query: $query");
    
    //     return $query;
    // }

    private function getUserInteractions($userId)
    {
        $interactions = BookInteraction::where('user_id', $userId)
            ->select('book_id', 'score')
            ->get()
            ->mapWithKeys(function ($interaction) {
                return [$interaction->book_id => $interaction->score];
            })
            ->toArray();
    
        Log::info("Retrieved interactions for user $userId:", $interactions);
    
        return $interactions;
    }

    private function getAllUsers()
    {
        return BookInteraction::select('user_id', 'book_id', 'score')
            ->get()
            ->groupBy('user_id')
            ->map(function ($interactions) {
                return $interactions->keyBy('book_id')
                    ->map(function ($interaction) {
                        return $interaction->score;
                    })
                    ->toArray();
            })
            ->toArray();
    }

    private function calculateSimilarities($userId, $userInteractions, $allUsers)
    {
        $similarities = [];

        foreach ($allUsers as $otherUserId => $otherUserInteractions) {
            if ($otherUserId == $userId) continue;

            $commonBooks = array_intersect_key($userInteractions, $otherUserInteractions);
            if (count($commonBooks) > 0) {
                $similarity = $this->distanceMetric->distance(
                    array_values($commonBooks),
                    array_values(array_intersect_key($otherUserInteractions, $commonBooks))
                );
                $similarities[$otherUserId] = 1 / (1 + $similarity); // Convert distance to similarity
            }
        }

        arsort($similarities);
        return $similarities;
    }

  private function getTopRecommendations($similarities, $userInteractions, $limit)
  {
      $recommendations = [];
      $seenBooks = array_keys($userInteractions);

      foreach ($similarities as $similarUserId => $similarity) {
          $similarUserInteractions = $this->getUserInteractions($similarUserId);
          
          foreach ($similarUserInteractions as $bookId => $score) {
              if (!in_array($bookId, $seenBooks)) {
                  if (!isset($recommendations[$bookId])) {
                      $recommendations[$bookId] = 0;
                  }
                  $recommendations[$bookId] += $similarity * $score;
              }
          }
      }

      arsort($recommendations);
      $topRecommendations = array_slice($recommendations, 0, $limit, true);

      if (empty($topRecommendations)) {
          Log::info("No recommendations found. Using user's top rated books.");
          arsort($userInteractions);
          $topRecommendations = array_slice($userInteractions, 0, $limit, true);
      }
      if (empty($topRecommendations)) {
        Log::info("No top recommendations or user interactions found.");
        return [];
    }

      return $topRecommendations;
  }
}