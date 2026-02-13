<?php

namespace App\Services;

use App\Models\Hashtag;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

            $hashtags_uuid = $this->ExtractHashtag($post, $hashtags);
            if (!empty($hashtags_uuid)) {
                $post_uuid = Str::uuid();
                foreach ($hashtags_uuid as $hashtag_uuid) {
                    $posts[] = [
                        'content' => $post,
                        'uuid' => $post_uuid
                    ];
                    $matched_hashtags[] = [
                        'post_uuid' => $post_uuid,
                        'hashtag_uuid' => $hashtag_uuid
                    ];
                }
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
            "data" => [
                "posts" => $posts,
                "post_hashtag" => $matched_hashtags
            ]
        ];
        return $data;
    }

    private function ExtractHashtag(string $post, array $hashtags): array
    {
        $escaped = array_map('preg_quote', $hashtags);
        $extracted_hashtags_uuid = [];
        foreach ($escaped as $uuid => $hashtag) {
            if (preg_match("/(" . $hashtag . ")\b/i", $post)) {
                $extracted_hashtags_uuid[] = $uuid;
            }
        }
        return $extracted_hashtags_uuid;
    }
}
