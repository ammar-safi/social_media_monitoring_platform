<?php

namespace App\Services;

use App\Models\Hashtag;
use Exception;
use Illuminate\Support\Collection;

class ExtractPostsService
{
    public function extractPosts(string $path, array $hashtags, int $start_line): array
    {
        /*
            TODO 

            Validate the array , it have to be like this :
            [
                (int) id => (string) Hashtag,
                (int) id => (string) Hashtag,
                (int) id => (string) Hashtag,
                (int) id => (string) Hashtag
            ] 
        */
        $posts = [];
        $matched_hashtags = [];
        $start_line = $start_line ?? 0;
        if (empty($hashtags)) {
            throw new Exception("There is no hashtags");
        }
        $file = fopen($path, "r");
        if ($file == False) {
            throw new Exception("No such file or directory.");
        }

        $current_line = 0;
        $collected = 0;
        while (($row = fgetcsv($file)) !== False) {
            if ($current_line < $start_line) {
                $current_line++;
                continue;
            }

            $index = config("app.cursor_position");
            if (!isset($row[$index])) {
                $current_line++;
                continue;
            }
            $post = $row[$index];

            $has_hashtag = $this->ExtractHashtag($post, $hashtags);
            if (!empty($has_hashtag)) {
                $posts[] = $post;
                $matched_hashtags[] = 
                "hashtags" => $has_hashtag

                $collected++;
            }
            $current_line++;

            if ($collected >= 10) {
                break;
            }
        }
        fclose($file);

        $data = [
            "current_line" => $current_line,
            "posts" => $posts
        ];
        return $data;
    }

    private function ExtractHashtag(string $post, array $hashtags): array
    {
        $escaped = array_map('preg_quote', $hashtags);
        $extract_hashtags = [];
        foreach ($escaped as $key => $hashtag) {
            if (preg_match("/(" . $hashtag . ")\b/i", $post)) {
                $extract_hashtags[] = $key;
            }
        }
        return $extract_hashtags;
    }
}
