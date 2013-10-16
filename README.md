DZend
=====

DZend is a set of classes to wrap around `Zend`. It' goal is to make common
patterns easier to implement and to transparently add commonly used
functionalities.

If a Zend class is wrapped at DZend, then it's name will be the same as the Zend
class but with a D at the beginning, but there is no abstract tables at DZend.
For instance, `DZend_Db_Table` extends `Zend_Db_Table_Abstract`.

Bootstrap
---------

The bootstrap instantiate a few commonly used tools.

### Logger

Instantiate `Zend_Log` and write it on registry. To access this instance just
call:

    Zend_Registry::get('looger');

All the logs will be written on the file `public/tmp/log.txt`.

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

## `DZend_Db_Table`

The class `DZend_Db_Table` implements the `__call` magic function to add a few
functionalities to select queries. For instance, let `DbTable_User` extends
`DZend_Db_Table` and represents the `user` database table. To find all female
users, just use the call `$userDb->findByGender('f')`, where `$userDb` is an
instance of `DbTable_User`. DZend parses the function name recognize the
columns.

The "And" can be used to separate column names. The method of format
`findByColumnAAndColumnBAndColumnC($a, $b, $c)` will return a `Zend_Db_Table_Rowset`
containing all rows that have `column_a = $a AND column_b = $b AND column_c = $c`.

To fetch just a `Zend_Db_Table_Row` instead of a rowset, use `findRowBy`,
instead of `findBy`.

Similar to `findBy`, there is also `deleteBy`, which deletes all matching rows.

## `DZend_Db_Table_Row`

Usually SQL names for databases, tables and columns uses underscore notation
while programming uses camel notation. `DZend_Db_Table_Row` automatically
converts between one another. Let `$row` be an instance of `DZend_Db_Table_Row`.
`$row->columnName` can be used to access the column value for `column_name`.

CurrentUser
-----------

The pre-requisite to get this trait working properly is to have the auth to
store the currently logged in user row on
`DZend_Session_Namespace::get('session')->user`. It assumes that the `id`
attribute holds the primary key for the user.

`DZend_CurrentUser` is a trait that provider two methods, `_getUserRow()` and
`_getUserId()`. In case there is not user logged, in it returns `null`.

By default, `DZend_Model` and `DZend_Db_Table` already extends this trait.


PHPUnit
-------

TODO...



