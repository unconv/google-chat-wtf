<?php
namespace ChatWTF;

class ChatWTFResponse
{
    public function __construct(
        public string $question,
        public string $answer,
    ) {}
}
