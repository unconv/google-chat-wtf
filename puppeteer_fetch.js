const puppeteer = require('puppeteer');

const url = process.argv[2];

(async function( url ) {
    // launch browser
    const browser = await puppeteer.launch( {
        headless: true,
    } );

    // open the url
    const page = await browser.newPage();
    try {
        await page.goto( url, {
            waitUntil: "domcontentloaded",
            timeout: 10000
        } );
    } catch ( error ) {
        // i didn't want to wait that long anyway
    }

    // wait for website source to stabilize
    try {
        await page.waitForFunction( async() => {
            let html = document.body.innerHTML;
            await new Promise( _ => setTimeout( _, 2000 ) );
            return document.body.innerHTML === html;
        }, {
            timeout: 10000,
        } );
    } catch( error ) {
        // i didn't want to wait that long anyway
    }

    // get html of page
    const html = await page.evaluate( () => {
        if( document.getElementsByTagName("main").length ) {
            return document.getElementsByTagName("main")[0].innerHTML;
        }
        return document.body.innerHTML;
    } );

    // close browser
    await browser.close();

    // print html
    console.log( html );
})( url );
