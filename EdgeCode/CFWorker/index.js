addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
    request = new Request(request)
    let requestedURL = new URL(request.url);
    let urlPath = trimByChar(requestedURL.pathname, '/')
    if (urlPath == '/') {
        urlPath = 'home'
    }
    const kvdata = await kvstore.get(urlPath);
    if (kvdata !== null) {
        var requestTimestamp = new Date().toUTCString();
        var myHeaders = new Headers({
            'Content-Type': 'text/html; charset=utf-8',
            'vary': 'X-Forwarded-Protocol,Accept-Encoding',
            'server': 'Edge Hosting',
            'date': requestTimestamp,
            'x-worker-debug': 'KV Active'
        });
        let response = new Response(kvdata, {headers: myHeaders})

        return response
    } else {
        let originResponse = await fetch(request)
        let response = new Response(originResponse.body, originResponse)
        response.headers.set('x-worker-debug', 'Origin Active')
        response.headers.set('x-path', urlPath);
        return response
    }
}

function trimByChar(string, character) {
    const first = [...string].findIndex(char => char !== character);
    const last = [...string].reverse().findIndex(char => char !== character);
    return string.substring(first, string.length - last);
}