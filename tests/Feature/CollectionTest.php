<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $arr = [1,2,3];
        $collection = collect($arr);
        // revert data type collection to array with method all()
        $this->assertEquals($arr, $collection->all());
        // equal with ignore the index of array
        $this->assertEqualsCanonicalizing($arr, $collection->all());
    }
}
