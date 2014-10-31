magento-sorted-db-layouts
======================

Modifies (fixes?) Magento 1.x core when fetching layout updates from core_layout. They are now applied in correct sort_order.

Useful when you want to specify sort order for widgets. Check [this stackexchange thread](http://magento.stackexchange.com/questions/14744/widgets-sort-order-in-different-design-packages) for an error description.

Install with composer
---------------------

```
    "require": {
        "gruenspar/magento-sorted-db-layouts":"1.*"
    },
    "repositories": [
        {
            "url": "https://github.com/gruenspar/magento-sorted-db-layouts",
            "type": "git"
        }
    ]
```

Rewrites
--------

- `Mage_Core_Model_Layout_Update`
- `Mage_Core_Model_Resource_Layout`
