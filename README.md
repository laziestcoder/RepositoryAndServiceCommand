# Repository and Service Command

## Instructions
Download the repository and extract it.

## Installation
####Step 1: 
Copy ```app/Console/Commands``` directory to your project ```app/Console/``` directory.
```$xslt
|-- app/Console/Commands
    |-- Repository.php (core file)
    |-- Service.php (core file)
```

####Step 2:
Copy ```config/repository.php``` file to your project ```config/``` directory.

```$xslt
|-- config
    |-- repository.php (core confid file)
```

####Step 3:
Add following commands to your ```app/Console/Kernel.php``` file. 
```$xslt
protected $commands = [
        Commands\Repository::class,
        Commands\Service::class,
    ];
```
####Step 4:
Edit the config file. Remember All the directories are under `app` directory.
```$xslt
  'repository_directory' => 'Repositories',  ## app/Repositories
  'model_directory' => 'Models', #app/Models
  'service_directory' => 'Services', #app/Models
```
###Core File Structure: 
```
|-- config/
    |-- repository.php
|-- Commands
    |-- Repository.php (core file)
    |-- Service.php (core file)
|-- README.md
```

## Usage
####Make Repository: 
The following command makes a repository class ```TestRepositoryName``` using model 
```TestModelName```.
```bash
php artisan make:repository TestRepositoryName TestModelName
```
####Make Repository with Service: 
The following command makes a repository class ```TestRepositoryName``` using model 
```TestModelName``` and a service ```TestServiceName```.
```bash
php artisan make:repository TestRepositoryName TestModelName --service=TestServiceName
```
or
```bash
php artisan make:repository TestRepositoryName TestModelName --s=TestServiceName
```

Service flag ```--service``` or ```--s``` is optional.

####Make Service: 
The following command makes a service class ```TestServiceName``` using repository 
```TestRepositoryName```.

```bash
php artisan make:service TestServiceName TestRepositoryName 
```


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.
##Inspired By: 
##### [Mohammad Imran Hossain](https://github.com/imranctg16).

Very much grateful to @imranctg16.
## License
[MIT License](https://choosealicense.com/licenses/mit/)

Copyright (c) 2019 [Towfiqul Islam](https://github.com/laziestcoder/)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.