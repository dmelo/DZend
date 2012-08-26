DZend
=====

DZend is a set of classes to wrap around `Zend`. It' goal is to make common
patterns easier to implement and to transparently add commonly used
functionalities.

Bootstrap
---------

The bootstrap instantiate a few commonly used tools.

### Logger

Instantiate `Zend_Log` and write it on registry. To access this instance just
call:

    Zend_Registry::get('looger');

All the logs will be written to the file `public/tmp/log.txt`.

### Internationalization

A `Zend_Translate` instance is already included on the registry `translate`.
Similarly to the logger, to access the translate object, call:

    Zend_Registry::get('translate');

The detection of the locale is made automatically, in case none is found,
`en_US` is the default.


Controller
----------

TODO...


Database
--------

TODO...


PHPUnit
-------

TODO...
