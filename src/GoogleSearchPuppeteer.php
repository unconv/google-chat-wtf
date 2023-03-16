<?php
namespace ChatWTF;

class GoogleSearchPuppeteer
{
    const SEARCH_URL = "https://www.google.com/search?hl=en";

    /**
     * @return SearchResult[]
     */
    public function search( string $search_query ): array {
        $fetcher = new URLFetcher(
            static::SEARCH_URL . "&q=" . urlencode( $search_query )
        );
        $source = $fetcher->get_source();

        // remove "news" section
        $source = preg_replace( '/<g-section-with-header.*?<\/g-section-with-header>/is', '', $source );

        $results = [];

        preg_match_all( '/href="(https?:\/\/[^"]+?)"/', $source, $links );

        if( ! isset( $links[1] ) ) {
            throw new \Exception( "No Google search results found" );
        }

        foreach( $links[1] as $link ) {
            if( strpos( $link, ".google" ) !== false ) {
                continue;
            }

            if( strpos( $link, "par=google" ) !== false ) {
                continue;
            }

            if( strpos( $link, "twsrc" ) !== false ) {
                continue;
            }

            $result = new SearchResult(
                url: $link,
                title: "",
                description: "",
            );

            $results[] = $result;
            
        }

        return $results;
    }
}
