const NODE_ENV = process.env.NODE_ENV || 'development';

var path = require('path');

module.exports = {
    mode: NODE_ENV,
    entry: './modules/checklists/assets/js/gutenberg-warning.jsx',
    output: {
        path: path.join(__dirname, 'modules/checklists/assets/js'),
        filename: 'gutenberg-warning.min.js'
    },
    module: {
        rules: [
            {
                test: /.jsx$/,
                exclude: /node_modules/,
                loader: 'babel-loader'
            }
        ]
    }
};
