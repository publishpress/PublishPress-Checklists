# PublishPress Checklists


## Description

Extend PublishPress implementing a list of requirements to publish posts.

## Documentation

https://publishpress.com/docs/

## How to report bugs or send suggestions

Feel free to email us via [help@publishpress.com](mailto:help@publishpress.com). We would love to hear you, and will work hard to help you.

### Guidelines

* Write a clear summary
* Write precise steps to reproduce

## How to contribute with code

* Clone the repository
* Create a new branch
* Implement and commit the code
* Create a Pull Request targetting the "development" branch adding details about your fix

We will review and contact you as soon as possible.

## Development

### Setup

Before starting developing you need to install some dependencies managed by composer and npm.
Make sure you have composer and npm installed and working.

```shell script
$ composer update
$ npm install
``` 

### React

#### Compiling JSX files to JS

While developing you can set webpack to watch for file changes:

```
$ npm run dev
```

For building the package for production:

```
$ npm run build 
```

### Building

#### Changing the version number

```shell script
$ phing set-version
```

#### Building the package

```shell script
$ phing build
```

## License

License: [GPLv2 or later](http://www.gnu.org/licenses/gpl-2.0.html)
