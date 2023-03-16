<?php
namespace ChatWTF;

use Orhanerday\OpenAi\OpenAi;

class ChatWTF
{
    private string $default_prompt;

    /**
     * @var ChatWTFResponse[]
     */
    private array $sample_questions;

    /**
     * @var ChatWTFResponse[]
     */
    private array $context;

    public function __construct(
        protected OpenAi $open_ai_api,
    ) {}

    public function set_default_prompt( string $prompt ) {
        $this->default_prompt = $prompt;
    }

    /**
     * @param ChatWTFResponse[] $questions
     */
    public function set_sample_questions( array $questions ) {
        $this->sample_questions = $questions;
    }

    public function add_to_context( ChatWTFResponse $response ) {
        $this->context[] = $response;
    }

    public function ask( string $question ): ChatWTFResponse {
        $prompt = $this->create_prompt( $question );

        $prompt = preg_replace( '/[[:^print:]]/', '', $prompt );

        $complete = json_decode( $this->open_ai_api->completion( [
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'temperature' => 0.9,
            'max_tokens' => 2000,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
            'stop' => [
                "\nNote:",
                "\nQuestion:"
            ]
        ] ) );

        // get message text
        if( isset( $complete->choices[0]->text ) ) {
            $answer = str_replace( "\\n", "\n", $complete->choices[0]->text );
        } elseif( isset( $complete->error->message ) ) {
            $answer = $complete->error->message;
        } else {
            $answer = "Sorry, but I don't know how to answer that.";
        }

        return new ChatWTFResponse(
            question: $question,
            answer: $answer,
        );
    }

    private function create_prompt( string $question ) {
        // set default prompt
        $prompt = $this->default_prompt;

        // add context to prompt
        if( empty( $this->context ) ) {
            // if there is no context, use a default example question and answer
            foreach( $this->sample_questions as $sample_question ) {
                $prompt .= "Question:\n'".$sample_question->question."'\n\nAnswer:\n".$sample_question->answer."\n\n";
            }
            $please_use_above = "";
        } else {
            // add old questions and answers to prompt
            $prompt .= "";
            $context = array_slice( $this->context, -5 );
            foreach( $context as $message ) {
                $prompt .= "Question:\n" . $message->question . "\n\nAnswer:\n" . $message->answer . "\n\n";
            }
            $please_use_above = ". Please use the questions and answers above as context for the answer.";
        }

        // add new question to prompt
        $prompt = $prompt . "Question:\n" . $question . $please_use_above . "\n\nAnswer:\n\n";

        return $prompt;
    }
}
