Krumo your `drupal_debug.txt`
============

This project lets you view drupal_debug.txt files using krumo. 

When would this ever come up?
============

- You are working on a [Drupal](http://drupal.org) site.
- You use the [Devel](http://drupal.org/project/devel) module.
- You use `dpm()`.
- You want to `dpm()` something (array(s) or object(s)) that is so huge it eats all your PHP memory and throws a fatal error.
- You use `dd()` instead because it doesn't crash Drupal.
- `drupal_debug.txt` is really difficult to parse and you wish you could view it with krumo.

Install
=======

1. After cloning this repo, copy the `default.config.php` and rename your copy `config.php`.
2. Point `DEBUG_FILE_PATH` at your `drupal_debug.txt` file.
3. (Optional) If your Drupal projects use the same directory structure for their temporary files, change `PROJECT` each time you want to view a different `drupal_debug.txt` file.

Troubleshooting
=========

If you get a whitescreen, clean out your `drupal_debug.txt` file. Lots of huge objects and arrays will still eat all your PHP memory.

Also, this won't print random strings that you `dd()`. Only `stdClass Object`s and `Array`s.
