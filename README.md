# figuren.theater | Options

Options Management for a WordPress multisite network like [figuren.theater](https://figuren.theater).

---

## Plugins included

This package contains the following plugins. 
Thoose are completely managed by code and lack of their typical UI.

* [markjaquith/wp-tlc-transients](https://packagist.org/packages/markjaquith/wp-tlc-transients)
    A WP transients interface with support for soft-expiration, background updating of the transients. <br/>*(Not a real plugin, but a powerful external library, that is autoloaded via composer.)*

---

## What does this package do in addition?

Accompaniying the core functionality of the mentioned plugins, theese **best practices** are included with this package.

* Provide an API for handling WordPress `options` and `site_options` completely static via code by utilising some functions around the `pre_option_${option_name}` filter. The API allows options to be either:

    + *static*<br/>
       The same code-defined option value is used througout the complete network of all sites.
    +  *synced*<br/>
       The option value used, is taken from another site within the multisite network.
    +  *merged*<br/>
       This is handy especially for options containing array or objects of different data. This option-type mixes static option values, provided by code, with dynamic data created by and for the current site.

---

## Todo


* [ ] Write better Readme
