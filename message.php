<?php
namespace ChatWTF;

require_once(__DIR__."/vendor/autoload.php");
require_once(__DIR__."/error_handler.php");

$settings = require(__DIR__."/settings.php");

use Orhanerday\OpenAi\OpenAi;

header( "Content-Type: application/json" );

// get chat context
$context = json_decode( $_POST['context'] ?? "[]" ) ?: [];

// initialize OpenAI api
$openai = new OpenAi( $settings['open_ai_api_key'] );

// initialize Google search class
if( $settings['google_search_method'] === "puppeteer" ) {
    $google = new GoogleSearchPuppeteer();
} else {
    $google = new GoogleSearch(
        api_key: $settings['google_search_api_key'],
        engine_id: $settings['google_search_engine_id']
    );
}

// ask OpenAI for a relevant Google search
$chatwtf = new GoogleSearchChatWTF( $openai );
$search_query = $chatwtf->ask( $_POST['message'] )->answer;

try {
    // get search results
    $search = $google->search( $search_query );
} catch ( \Exception $e ) {
    // or send error message
    echo json_encode( [
        "message" => $e->getMessage(),
        "raw_message" => "",
        "status" => "success",
    ] );
    exit;
}

// loop through results
foreach( $search as $result ) {
    // fetch url of search result
    $fetcher = new URLFetcher(
        url: $result->url,
        load_iframes: true
    );

    // get text content of result page
    $text_content = $fetcher->get_text_content();

    // limit text content
    $text_content = substr( $text_content, 0, 5000 );

    // initialize ChatWTF
    $chatwtf = new ResultInterpreterChatWTF( $openai );

    // set results
    $chatwtf->set_results( $text_content, $result->url );

    // add chat message context
    if( ! empty( $context ) ) {
        $context = array_slice( $context, -5 );
        foreach( $context as $message ) {
            $chatwtf->add_to_context( new ChatWTFResponse(
                question: $message[0],
                answer: $message[1]
            ) );
        }
    }
    
    // ask question from ChatWTF
    $response = $chatwtf->ask( $_POST['message'] );

    // if question was answered, break, otherwise
    // go to next result page
    if( $chatwtf->last_response_is_positive() ) {
        break;
    }
}

// create message
$message = $response->answer;
$message .= "<br /><br />Source: " . $result->url;

// return response
echo json_encode( [
    "message" => $message,
    "raw_message" => $response->answer,
    "status" => "success",
] );
