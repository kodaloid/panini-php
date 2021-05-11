![Panini PHP Logo](https://kodaloid.com/wp-content/uploads/2018/12/panini-php.png)

# PaniniPHP
A compact, Composer friendly PHP framework for building web apps.

Add as much or as little as you want, the point is you start without the kitchen sink!

* Bundles with Symphony/Twig.
* Stupidly fast, loading only what's needed to get your views rendered.
* Initial package including vendor files is less than 1mb, less than 400 files.
* Has an easy to use routing system, with automatic controller and view discovery.
* Output to Twig, XML, Soap/JSON.
* Works great alongside popular projects such as React, AngularJS and VueJS.


## The Goal

The goal of Panini PHP is to provide a slim equivalent of modern frameworks like
CakePHP and Laravel. It handles the essential features you need such as database
management, user management/permissions, ajax calls, caching, etc... and leaves
the rest to you.

The entire engine can be found under the system sub-directory. It's made of a
handful of carefully thought out PHP files, and I encourage you to have a look
through them to see how it works.

## Requirements

* PHP 7.1+ Minimum (tested) / 7.4+ Recommended
* Composer (https://getcomposer.org/)
* A cup of coffee!


## Changelog

All updates are documented in a changelog file [here](/CHANGELOG.md).


## Getting Setup (the one minute install)

1. Download/clone this repo into a folder.
2. Open a terminal and navigate to your project folder.
3. Restore the composer vendor files using `composer install`
4. Launch the site in your browser!


## The App Folder

The app folder holds all files that you'll want to create for your project. It
looks like this:

   ```
   app/controllers/
   app/models/
   app/views/
   app/app.php
   ```

Although Panini is not an MVC framework, it does attempt to adhere to that
methodology where possible. So the `controllers` folder hosts PHP files for
handling page requests. The `models` folder can be used (but not required) when
using ORM technologies like Eloquent. The `views` folder usually mirrors 
controllers but with the .twig extension. And `app.php` is a PHP script that
runs before anything else, giving you the opportunity to set-up things like
databases.

## Routing

Panini automatically routes URLs to the `app/controller/` folder. So for example
if you run from the root of your localhost, then `http://localhost/pages/about` 
will route to `app/controller/pages/about.php` and so on.

If a controller is not found, Panini looks for a `root.php` controller in
descending folder order till it finds a controller that exists, or reaches the
controllers folder root. So in the above example, if `about.php` did not exist,
Panini would search for a controller like this:

   ```
   app/controllers/pages/about.php
   app/controllers/pages/root.php
   app/controllers/root.php
   ```

Please note that a valid working controller is required for output when error
logs are disabled.


## Controllers

A controller file is a PHP script that tells Panini what to do when a URL is
requested. The most basic controller file can look like this:

   ```
   <?php

   $this->present_view(null, []);
   ```

In the example above, `$this` is the App class instance. And calling the member
function `present_view` asks for a view path & arguments. Here I've told Panini
to load a .twig file template with the same name from the `app/views` folder by
just passing a null, however you can indicate a different one by replacing null
with the relative path to another .twig file template.


## Modules

Out of the box Panini PHP serves web-pages with the twig engine, but doesn't
have any awareness of advanced functionality like databases etc... It's expected
that you will want to use Composer to add packages to enhance things.

However sometimes you just want things to work. So Panini comes with several
installable *modules* to cover the basics.

Here's an example of how easy it is to set-up and use MySQL using modules:

   ```
   $db = $this->load_module(
      'database',
      'MySQL_Database',
      [ DB_HOST, DB_NAME, DB_USER, DB_PASS ]
   );
   ```

Notice `$this` represents the app. Once we've loaded the module into the app, we
can use it from then on by using `$this->modules->database` anywhere in our app
folder.

Taking the database example further, in a controller, you could then write the
following to read records from a table on the database:

   ```
   $data = $this->modules->database->select('SELECT * FROM users');

   $this->present_view('users', ['users' => $data]);
   ```

Then iterate through them using twig in your `users.twig` view like this:

   ```
   <ul>
   {% for user in users %}
      <li>{{ user.name }}</li>
   {% endfor %}
   </ul>
   ```

## License

The project will always be free to use and modify under the MIT license [here](/LICENSE).

## Contributing &amp; Support

There is a lot of scope to improve this project. Please take advantage of the
issues and improvement features of this repo to make suggestions. If you would
like to become a contributor, please visit my site (http://kodaloid.com/) and
contact me using my details there.