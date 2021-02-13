# Affiliate locator

Take a set of affiliate contact records from a text file or string and filter them down to your criteria.

Artisan command:

```bash
php artisan locate:within 100km --latitude=53.33 --longitude=-6.25
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
