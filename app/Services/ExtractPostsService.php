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
        $posts = [];
        $matched_hashtags = [];
        $start_line = $start_line ?? 0;
        $index = config("app.post_index");

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

            if (!isset($row[$index])) {
                $current_line++;
                continue;
            }
            $post = $row[$index];

            $hashtags_uuid = $this->ExtractHashtag($post, $hashtags);
            if (!empty($hashtags_uuid)) {
                $post_hash = hash('sha256', $post);
                $posts[] = [
                    'uuid' => Str::uuid(),
                    'hash' => $post_hash,
                    'content' => $post,
                ];
                foreach ($hashtags_uuid as $hashtag_uuid) {
                    $matched_hashtags[] = [
                        'post_hash' => $post_hash,
                        'hashtag_uuid' => $hashtag_uuid
                    ];
                }
                $collected++;
            }

            $current_line++;
            if ($collected >= (int) config("app.count_of_row")) {
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
            if (preg_match("/\b{$hashtag}\b/iu", $post)) {
                $extracted_hashtags_uuid[] = $uuid;
            }
        }
        return $extracted_hashtags_uuid;
    }
}
