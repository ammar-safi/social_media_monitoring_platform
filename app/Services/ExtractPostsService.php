<?php

namespace App\Services;

use Exception;

class ExtractPostsService
{
    public function extractPosts(string $path, array $hashtags, int $start_line): array
    {
        $posts = [];
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

            $index = env("COMMENT_POSITION_IN_CSV", 3);
            if (!isset($row[$index])) {
                $current_line++;
                continue;
            }
            $post = $row[$index];

            $has_hashtag = $this->DoseItContainHashtag($post, $hashtags);
            if ($has_hashtag) {
                $posts[] = [
                    "content" => $post
                ];
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

    private function DoseItContainHashtag(string $post, array $hashtags): bool
    {
        $escaped = array_map('preg_quote', $hashtags);
        $pattern = "/(" . implode("|", $escaped) . ")\b/i";
        return preg_match($pattern, $post);
    }
}
