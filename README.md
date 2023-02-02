<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/figuren-theater/ft-options">
    <img src="https://raw.githubusercontent.com/figuren-theater/logos/main/favicon.png" alt="figuren.theater Logo" width="100" height="100">
  </a>

  <h1 align="center">figuren.theater | Options</h1>

  <p align="center">
    Options Management for a WordPress Multisite network like <a href="https://figuren.theater">figuren.theater</a>.
    <br /><br /><br />
    <a href="https://meta.figuren.theater/blog"><strong>Read our blog</strong></a>
    <br />
    <br />
    <a href="https://figuren.theater">See the network in action</a>
    •
    <a href="https://mein.figuren.theater">Join the network</a>
    •
    <a href="https://websites.fuer.figuren.theater">Create your own network</a>
  </p>
</div>

## About 


This is the long desc

* [x] *list closed tracking-issues or `docs` files here*
* [ ] Write better Readme
* [ ] Do you have any [ideas](/issues/new) ?

## Background & Motivation

...

## Install

1. Add this repository to your `composer.json`
```json
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/figuren-theater/ft-options"
    }
]
```

2. Install via command line
```sh
composer require figuren-theater/ft-options
```

## Usage

### API

```php
Figuren_Theater::API\get_...()
```

### Plugins included

This package contains the following plugins. 
Thoose are completely managed by code and lack of their typical UI.

* [markjaquith/wp-tlc-transients](https://packagist.org/packages/markjaquith/wp-tlc-transients)
    A WP transients interface with support for soft-expiration, background updating of the transients. <br/>*(Not a real plugin, but a powerful external library, that is autoloaded via composer.)*


### What does this package do in addition?

Accompaniying the core functionality of the mentioned plugins, theese **best practices** are included with this package.

* Provide an API for handling WordPress `options` and `site_options` completely static via code by utilising some functions around the `pre_option_${option_name}` filter. The API allows options to be either:

    + *static*<br/>
       The same code-defined option value is used througout the complete network of all sites.
    +  *synced*<br/>
       The option value used, is taken from another site within the multisite network.
    +  *merged*<br/>
       This is handy especially for options containing array or objects of different data. This option-type mixes static option values, provided by code, with dynamic data created by and for the current site.



## Built with & uses

  - [dependabot](/.github/dependabot.yml)
  - ....

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


## Versioning

We use [Semantic Versioning](http://semver.org/) for versioning. For the versions
available, see the [tags on this repository](/tags).

## Authors

  - **Carsten Bach** - *Provided idea & code* - [figuren.theater/crew](https://figuren.theater/crew/)

See also the list of [contributors](/contributors)
who participated in this project.

## License

This project is licensed under the [GPL-3.0-or-later](LICENSE.md), see the [LICENSE](LICENSE) file for
details

## Acknowledgments

  - [altis](https://github.com/search?q=org%3Ahumanmade+altis) by humanmade, as our digital role model and inspiration
  - [@roborourke](https://github.com/roborourke) for his clear & understandable [coding guidelines](https://docs.altis-dxp.com/guides/code-review/standards/)
  - [python-project-template](https://github.com/rochacbruno/python-project-template) for their nice template->repo renaming workflow
