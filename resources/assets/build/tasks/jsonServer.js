const fs = require('fs');
const path = require('path');
const config = require('../../config');
const colors = require('colors/safe');
const express = require('express');
const lodash = require('lodash');

module.exports = () => () => new Promise(resolve => {
    const server = express();

    server.use((req, res, next) => {
        res.header("Access-Control-Allow-Origin", "*");
        res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        next();
    });

    server.get('*', (request, response) => {
        const url = request.url.split('?')[0];

        const fileName = lodash
            .trim(url, '/')
            .replace(/\//g, '.')
            .replace(/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/g, '*');

        const filePath = path.resolve(config.jsonServer.db, `./${fileName}.json`);

        response.setHeader('Content-Type', 'application/json');

        if (fs.existsSync(filePath)) {
            response.send(
                fs.readFileSync(filePath)
            );

            console.log(
                `[${colors.green('%s')}] ${colors.blue('GET')}: %s ${colors.magenta('->')} %s`,
                'JSON Server',
                request.url,
                fileName
            );
        } else {
            response.status(500).send('Something broke!');

            console.log(
                `[${colors.green('%s')}] ${colors.blue('GET')} (${colors.red('Not found')}): %s ${colors.magenta('->')} %s`,
                'JSON Server',
                request.url,
                fileName
            );
        }
    });

    server.listen(config.jsonServer.port, () => {
        console.log(
            `[${colors.green('%s')}] %s`,
            'JSON Server',
            `http://localhost:${config.jsonServer.port}`
        );

        resolve();
    });
});
