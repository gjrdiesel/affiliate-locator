# Affiliate locator

Take a set of affiliate contact records from a text file or string and filter them down to your criteria.

Artisan command:

```bash
$ php artisan locate:within 100km --latitude=53.33 --longitude=-6.25
                              
+--------------+--------------------+
| Affiliate ID | Name               |
+--------------+--------------------+
| 4            | Inez Blair         |
| 5            | Sharna Marriott    |
| 6            | Jez Greene         |
| 8            | Addison Lister     |
| 11           | Isla-Rose Hubbard  |
| 12           | Yosef Giles        |
| 13           | Terence Wall       |
| 15           | Veronica Haines    |
| 17           | Gino Partridge     |
| 23           | Ciara Bannister    |
| 24           | Ellena Olson       |
| 26           | Moesha Bateman     |
| 29           | Alvin Stamp        |
| 30           | Kingsley Vang      |
| 31           | Maisha Mccarty     |
| 39           | Kirandeep Browning |
+--------------+--------------------+
```

Code example usage:

```php
use \Facades\App\AffiliateLocator;

// Returns a collection of affiliates that are within 10km of default coordinates (i.e. config/kax-media.php office)
AffiliateLocator::loadFile(base_path('affiliates.txt'))
    ->within('10km');
    
// Switch units
AffiliateLocator::loadFile(base_path('affiliates.txt'))
    ->within('10mi');
    
// Second parameter accepts an array of coordinates
AffiliateLocator::loadFile(base_path('affiliates.txt'))
    ->within('10km',[
        'latitude' => 53.3340285,
        'longitude' => -6.2535495
    ]);
```

## Testing

See test coverage [tests/Feature/AffiliateLocatorTest.php](tests/Feature/AffiliateLocatorTest.php)

## Known issues

See https://github.com/gjrdiesel/affiliate-locator/issues/1 for a list of known issues 😅
