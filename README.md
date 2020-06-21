#[Task](task.pdf)

# String Search 
A library for finding substrings in a string. Has the ability to add various search engines. 

## Install
### Install via subtree and composer
Include like subtree to scl/tools directory of project
Example: `git subtree add --squash --prefix=scl/composer/healthCheck git@gitlab.com:toartemii/string-search.git master`

````composer
{
    "require": {
        "scl/string-search": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url":  "scl/composer/string-search"
        }
    ]
}
````

### YII2 set singleton 
Add to bootstrap/SetContainer.php
```php
$container->setSingleton(StringSearch::class, function () {
    return new StringSearch();
});
```


## Usage examples
### Simple example: 
```php
$containerStringSearch = new StringSearch();
$result = $containerStringSearch->search('текст', 'test.txt');
```

### Select engine example: 
Using search with **BOYER MOOR** engine
```php
$containerStringSearch = new StringSearch();
$result = $containerStringSearch->search('текст', 'test.txt', StringSearch::ENGINE_BOYER_MOOR);
```

### Setting custom config: 
Using search with **BOYER MOOR** engine
```php
$containerStringSearch = new StringSearch('/app/config.yml');
$result = $containerStringSearch->search('текст', 'test.txt', StringSearch::DEFAULT_ENGINE);
```

example /app/config.yml
```yaml
MAX_FILE_SIZE: 20240000000
MAX_NEEDLE_SIZE: 1024
ALLOWED_MIME_FILE_TYPE_LIST:
  - text/plain

```

## Backlog
1. Work with remote files
1. Add unit-tests
1. Improve documentation describing DTOs, Exceptions, Engine work principe
