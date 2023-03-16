<?php
namespace ChatWTF;

use Orhanerday\OpenAi\OpenAi;

class ResultInterpreterChatWTF extends ChatWTF
{
    protected ChatWTFResponse $last_response;

    public function __construct(
        protected OpenAi $open_ai_api,
    ) {
        $this->set_sample_questions( [] );
    }

    public function ask( string $question ): ChatWTFResponse {
        $question = "According to the information above, tell me the answer to: '" . $question . '". If the answer cannot be determined, say "Undeterminable." Otherwise answer the question with a full sentence.';
        $this->last_response = parent::ask( $question );

        return $this->last_response;
    }

    public function set_results( string $results, string $source_url ) {
        $this->set_default_prompt(
            "## The following is an excerpt from " . $source_url . ": ###\n\n".
            $results
        );
    }

    public function last_response_is_positive() {
        return strpos( preg_replace( '/[^a-z]+/', '', strtolower( $this->last_response->answer ) ), "undeterminable" ) !== 0;
    }
}
