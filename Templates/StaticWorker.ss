addEventListener('fetch', event => {
    event.respondWith(handle(event.request))
})

let content = `
$PageContent
`;

async function handle(request) {

    return new Response(content, {
        status: 200,
        statusText: "OK",
        headers: {
            'cache-control': "max-age=100",
            'content-type': "text/html; charset=utf-8",
            'content-encoding': "gzip"
        }
    })
}