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
        // equal with ignore the index/key of collection
        $this->assertEqualsCanonicalizing($arr, $collection->all());
    }

    public function testForEach()
    {
        $collection = ([1,2,3,4,5,6,7]);
        foreach($collection as $index => $value){
            $this->assertEquals($index + 1, $value);
        }
    }

    public function testManipulateCollection()
    {
        $collection = collect([]);

        // method push(data) to insert data
        $collection->push(1,2,3);
        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());

        // method pop() to get and delete from last index 
        // of collection
        $resultPop = $collection->pop();
        $this->assertEquals(3, $resultPop);
        $this->assertEqualsCanonicalizing([1,2], $collection->all());

        // method prepend(data) to insert data at 
        // first index of collection
        $collection->prepend(4);
        $this->assertEqualsCanonicalizing([4,1,2], $collection->all());

        // method pull(key/index) to get and remove data 
        // at specified key/index of collection
        $resultPull = $collection->pull(0);
        $this->assertEquals(4, $resultPull);
        $this->assertEqualsCanonicalizing([1,2], $collection->all());

        // method put(key/index, data) to change data with key/index
        // of collection
        $collection->put(1, 5);
        $this->assertEqualsCanonicalizing([5,2], $collection->all());
    }
}
