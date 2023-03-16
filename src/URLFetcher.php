<?php
namespace ChatWTF;

use DOMDocument;

class URLFetcher
{
    protected string $source;

    public function __construct(
        protected string $url,
        protected bool $load_iframes = false,
    ) {
        $this->source = $this->fetch( $url );

        if( $this->load_iframes ) {
            $this->source .= $this->fetch_iframes( $this->source );
        }
    }

    public function get_source(): string {
        return $this->source;
    }

    public function get_text_content(): string {
        $text = substr( $this->source, 0, 50000 );

        $text = $this->remove_tags( [
            "style",
            "script",
            "nav",
            "noscript",
        ], $text );

        // add newlines after divs and paragraphs
        $text = str_replace( "</div>", "</div>\n", $text );
        $text = str_replace( "</p>", "</p>\n", $text );

        // add spaces after table cells
        $text = str_replace( "</th>", "</th> ", $text );
        $text = str_replace( "</td>", "</td> ", $text );

        // add spaces before and after spans
        $text = str_replace( "<span", " <span", $text );
        $text = str_replace( "</span>", "</span> ", $text );

        // strip tags
        $text = strip_tags( $text );

        // remove extra whitespace, preserving newlines
        $text = preg_replace( "/[^\S\r\n]+/", " ", $text );

        // remove lines less than 20 characters
        $text = preg_replace( "/^.{0,20}$/", "", $text );

        // remove duplicated newlines
        $text = preg_replace( "/\n+/", "\n", $text );

        return $text;
    }

    private function remove_tags( array $tag_names, string $page_source ) {
        libxml_use_internal_errors( true );

        $dom = new DOMDocument();
        $dom->loadHTML( $page_source );

        foreach( $tag_names as $tag_name ) {
            $tags = $dom->getElementsByTagName( $tag_name );

            foreach( $tags as $item ) {
                $item->parentNode->removeChild( $item ); 
            }
        }

        $page_source = $dom->saveHTML();

        return $page_source;
    }

    private function fetch( string $url ): string {
        $script_dir = realpath( __DIR__ . "/../" );

        exec(
            "cd ".$script_dir."; ".
            "node puppeteer_fetch.js " . escapeshellarg( $url ),
            $output
        );

        return implode( "\n", $output );
    }

    private function fetch_iframes( string $page_source ): string {
        $iframes_source = "";

        libxml_use_internal_errors( true );

        $dom = new DOMDocument();
        $dom->loadHTML( substr( $page_source, 0, 15000 ) );

        $iframes = $dom->getElementsByTagName( "iframe" );
        foreach( $iframes as $iframe ) {
            $url = $iframe->getAttribute( "src" );

            if( empty( $url ) ) {
                continue;
            }

            if(
                strpos( $url, "yimg" ) !== false ||
                strpos( $url, "googletagmanager" ) !== false ||
                strpos( $url, "googlesyndication" ) !== false ||
                strpos( $url, "/cross-domain" ) !== false ||
                strpos( $url, "adsystem" ) !== false ||
                strpos( $url, ".png" ) !== false ||
                strpos( $url, ".gif" ) !== false ||
                strpos( $url, ".jpg" ) !== false ||
                strpos( $url, ".jpeg" ) !== false
            ) {
                continue;
            }

            $iframes_source .= $this->fetch( $url );
        }

        return $iframes_source;
    }
}
