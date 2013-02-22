Laravel 4 Wrapper for Zend\Http\Client a gateway for using a remote API as your data store
------------------------

1) allows for configuration via a config file

2) provides magic methods for all verbs

NOTE: assumes plural endpoint contains the data portion of the response

e.g. 

````
{users => [{"user":123},{"user":456}]}
````