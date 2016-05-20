# PhanUnsetPlugin

Disallow use of `eval` and `create_function`.

It is impossible to accurately analyze type information statically with these functions.

# Installation
1. Add `"jazzdan/phan-unset-plugin"` to your `composer.json` file.
2. `composer update && composer install`
3. Add an entry `'vendor/jazzdan/phan-eval-plugin/src/EvalPlugin.php',` to your phan config file's plugins array stanza.
