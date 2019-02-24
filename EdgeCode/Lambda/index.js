'use strict';

const zlib = require('zlib');
var AWS = require('aws-sdk');

exports.handler = (event, context, callback) => {

    var ddb = new AWS.DynamoDB.DocumentClient({region: 'us-east-1'});

    const request = event.Records[0].cf.request;
    var dbKey = request.uri.replace(/^\/+/g, '');

    var params = {
        TableName: "webtest",
        Key: {
            urlsegment: dbKey
        }
    };

    ddb.get(params, function (err, data) {

        if (err) {
            var buffer = zlib.gzipSync('Not Found');
            var resCode = '404';
        } else {
            var buffer = zlib.gzipSync(data['Item']['content']);
            var resCode = '200';
        }

        const base64EncodedBody = buffer.toString('base64');

        var response = {
            headers: {
                'content-type': [{key: 'Content-Type', value: 'text/html; charset=utf-8'}],
                'content-encoding': [{key: 'Content-Encoding', value: 'gzip'}],
                'cache-control': [{key: 'Cache-Control', value: 'max-age=1800'}],
                'x-server': [{key: 'X-Server', value: 'Service=Edge Host'}]
            },
            body: base64EncodedBody,
            bodyEncoding: 'base64',
            status: resCode,
            statusDescription: "OK"
        }

        callback(null, response);



    });

};