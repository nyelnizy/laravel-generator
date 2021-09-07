<h1 align="center"><img src="https://assets.infyom.com/open-source/infyom-logo.png" alt="InfyOm"></h1>

InfyOm Laravel Generator
==========================

[![Total Downloads](https://poser.pugx.org/infyomlabs/laravel-generator/downloads)](https://packagist.org/packages/infyomlabs/laravel-generator)
[![Monthly Downloads](https://poser.pugx.org/infyomlabs/laravel-generator/d/monthly)](https://packagist.org/packages/infyomlabs/laravel-generator)
[![Daily Downloads](https://poser.pugx.org/infyomlabs/laravel-generator/d/daily)](https://packagist.org/packages/infyomlabs/laravel-generator)
[![License](https://poser.pugx.org/infyomlabs/laravel-generator/license)](https://packagist.org/packages/infyomlabs/laravel-generator)

Generate Admin Panels CRUDs and APIs in Minutes with tons of other features and customizations with 3 different themes.  
Read [Documentation](https://www.infyom.com/open-source) for detailed installation steps and usage.

## About
This is just a bit of modification to a fork of the original  [Laravel Infyom Labs Generator](https://www.infyom.com/open-source).

### Installation
<h2>Add the following repositories to your composer.json file</h2>
 "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/nyelnizy/swagger-generator.git"
        },
        {
            "type": "vcs",
            "url": "https://github.com/nyelnizy/laravel-generator.git"
        },
        {
            "type": "vcs",
            "url": "git@github.com:InfyOmLabs/swaggervel.git"
        }
    ],

<h2>Add the following dependency to require block in composer.json</h2>

 "infyomlabs/laravel-generator": "dev-fix-1.0",
 "appointer/swaggervel": "dev-master"

 <b>Then run composer update</b>
