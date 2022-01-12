# Open API client generator
A tool to generate a PHP client out of an openapi documentation 

Currently only supports json format as a source.

## Usage
Run `src/generator.php` to generate the client. See it's help page

## Limitations
* Currently, only supports Json payloads
* Requires all paths to have a `tag` and an `operationId` defined

