# Google Search ChatGPT Chatbot

This is a chatbot that uses the OpenAI API to generate a Google search query based on the given question and then searches Google with that search query and answers the question based on the Google search results.

Watch the YouTube video where I made it here: https://www.youtube.com/watch?v=NybojHhnv_8

## Installation

This chatbot uses the `orhanerday/open-ai` composer package to connect to the OpenAI API. You need to run `composer install` to install that package.

It also (optionally) uses Puppeteer to fetch the Google search results page, so you need to run `npm install` to install Puppeteer. If you want to use the official Google Search API instead, you don't need to install Puppeteer.

## Settings

You need to rename the `settings.sample.php` to `settings.php` and you need to add your OpenAI API key in it. If you wish to use the official Google Search API, you also need to change the `google_search_method` in the settings to `api` and add your Google Search [API key](https://developers.google.com/custom-search/v1/overview) and [Engine ID](https://programmablesearchengine.google.com/controlpanel/create).

By default it uses Puppeteer to fetch the Google results page and parses the results that way.

## Based on ChatWTF

I created this chatbot based on my other project, [ChatWTF](https://github.com/unconv/chat-wtf), that answers programming questions ChatGPT-style. That's why the code is filled with references to ChatWTF, lol.

## Support

If you like this code or use it in some useful way, consider buying me a coffee: https://www.buymeacoffee.com/unconv
