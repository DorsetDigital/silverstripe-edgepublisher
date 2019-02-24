'use strict';

const zlib = require('zlib');

exports.handler = (event, context, callback) => {

    const request = event.Records[0].cf.request;
    var dbKey = request.uri.replace(/^\/+/g, '');

// Load the AWS SDK for Node.js
    var AWS = require('aws-sdk');
// Set the region
    AWS.config.update({region: 'us-east-1'});

// Create the DynamoDB service object
    var ddb = new AWS.DynamoDB({apiVersion: '2012-08-10'});

    var params = {
        TableName: 'webtest',
        Key: {
            'urlsegment': {S: dbKey}
        },
        ProjectionExpression: 'content'
    };
    var bodyContent = '';

// Call DynamoDB to read the item from the table
    ddb.getItem(params, function (err, data) {
        if (err) {
            bodyContent = 'Bugger';
        } else {
            bodyContent = data.Item;
        }
    });


    const buffer = zlib.gzipSync(bodyContent);
    const base64EncodedBody = buffer.toString('base64');

    var response = {
        headers: {
            'content-type': [{key: 'Content-Type', value: 'text/html; charset=utf-8'}],
            'content-encoding': [{key: 'Content-Encoding', value: 'gzip'}],
            'cache-control': [{key: 'Cache-Control', value: 'max-age=90'}],
            'x-mechanism': [{key: 'x-mechanism', value: 'service=Edge Host'}]
        },
        body: base64EncodedBody,
        bodyEncoding: 'base64',
        status: '200',
        statusDescription: "OK"
    }

    callback(null, response);
};