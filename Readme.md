## What does this bundle do?

This bundle provides a functionality to perform duplicate checks against
your entities. Especially in the CRM context you will encounter in the issue
where you database is full of duplicates.

## Usage

To enable the duplicate check for a given entity just, enable the check via
the entity config:

``` php
<?php
/**
 * @ORM\Entity
 * @Config(
 *  defaultValues={
 *      "duplicate"={
 *          "enabled"=true
 *      }
 *  }
 * )
 */
class MyEntity
{
    /**
     * @Config(defaultValues={"duplicate"={"enabled"=true}})
     */
    protected $name:
}
```

The bundle will perform a entity check, if an entity is created or updated
automatically. 

### Trigger manually

If you want to perform a check manually, you can use the `Facade` class.
This class performs a check and save the result immediately.

Please beware, that this check will might consume multiple megabyte of ram, 
depending of you database.

## Create your own adapter

Creating your own adapter is really simple. This bundle follows the
Adapter Pattern. So just create a class which implements the AdapterInterface
and tag this service with the tag "dmk_duplicate_check.adapter". 
  