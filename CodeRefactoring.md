# Introduction #

This document holds ideas and thoughts about refactoring the code, creating several internal API's and possibly enable external developers as wel as current ones to easily re-use code and keep new code as clean as possible.

# Targets #

  * PHP4 compatibility (need stats on usage!)
  * avoiding PHP 5.3.x 'deprecated' messages
  * avoid duplication code


# Suggested API's #
## Store API ##

Implements data-agnostic storage

### implementation notes ###
-

### Functions ###
```
function
```

## Config API ##

Implements a way of reading, writing, importing and exporting configuration data

### Implementation notes ###
  * Keys and namespaces MUST match the following regex: `/^[a-z][a-z0-9-]+[a-z0-9]$/`

### Functions ###
```
get($namespace, $key, $def_value = '')
set($namespace, $key, $value)
delete($namespace, $key = '')
save()
import($data = array())
export()
```