<?php
namespace ChatWTF;

class SearchResult
{
    public function __construct(
        public string $url,
        public string $title,
        public string $description,
    ) {}
}
