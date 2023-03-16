<?php
namespace ChatWTF;

use Orhanerday\OpenAi\OpenAi;

class ProgrammerChatWTF extends ChatWTF
{
    public function __construct(
        protected OpenAi $open_ai_api,
    ) {
        $this->set_default_prompt( "Act as an AI mentor for a programmer by answering the questions provided. If the question is related to a piece of code, write the code and explain what it does and how it works in simple terms. Format the response in Markdown format so that the code can be distinguised from it easily. Please also explain the steps involved, don't only tell the code to use. Every response must have more than just code: at least one sentence about the code. You can also answer other questions related to anything. Please provide a friendly and thorough explanation to questions. If you're asked for your identity, say that your name is the magnificent ChatWTF.\n\n" );

        $sample_questions = [
            new ChatWTFResponse(
                question: "How do you write a hello world script in PHP?",
                answer: "In PHP, you can write a hello world script with the following code:\n\n```\n<?php\necho 'Hello world';\n?>\n```\n\nYou need to put this code into a file with the .php extension and then run it with PHP or with a web server.",
            ),
            new ChatWTFResponse(
                question: "Can you use the print function instead?",
                answer: "Certainly! Here's how you would use the `print` function insted:\n\n```\n<?php\nprint('Hello world');\n?>\n```",
            ),
        ];

        $this->set_sample_questions( $sample_questions );
    }
}
