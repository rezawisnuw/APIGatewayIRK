<?php

namespace Tests\Mocks;

// MockIRKHelp.php

namespace Tests\Mocks;

use App\Helper\IRKHelp;

class MockIRKHelp extends IRKHelp
{
    public function Segment($slug)
    {
        // Return a predefined array for testing
        return [
            'authorize' => 'Authorization-dev',
            'config' => config('app.URL_DEV'),
            'path' => 'Dev',
        ];
    }
}
