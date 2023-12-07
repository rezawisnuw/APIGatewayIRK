<?php

namespace Tests\Unit\Helper;

use App\Helper\IRKHelp;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Response;
use Tests\TestCase;

class IRKHelpTest extends TestCase
{
    public function testSegment()
    {
        $request = new Request();
        $irkHelp = new IRKHelp($request);

        // Test with a valid slug
        $slug = 'live';
        $segment = $irkHelp->Segment($slug);

        $this->assertArrayHasKey('authorize', $segment);
        $this->assertArrayHasKey('config', $segment);
        $this->assertArrayHasKey('path', $segment);

        // Test with an invalid slug
        $slug = 'hahahihi';
        $result = $irkHelp->Segment($slug);

        // Ensure that the result is encrypted
        $this->assertTrue(Crypt::decryptString($result) !== false);
    }

}

