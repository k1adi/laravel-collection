<?php

namespace Tests\Feature;

use App\Data\Person;
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

    /** 
     * Mapping
     * transform data to another data
     * need a function as parameter to tranform the data
     * result index of mapping will be the same with the collection
    */

    public function testMap()
    {
        $collection = collect([1,2,3]);
        // Iterate all data and send all of the data to the function
        $result = $collection->map(function ($item) {
            return $item * 2;
        });

        $this->assertEqualsCanonicalizing([2,4,6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(['Rizki', 'Adi']);
        // Iterate all data and create new object for Class
        // with sending param of each data
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person('Rizki'), new Person('Adi')], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ['Rizki', 'Adi'],
            ['Asep', 'AC']
        ]);

        // Iterate all data and sending each data as param
        $result = $collection->mapSpread(function($firstName, $lastName) {
            $fullName = "$firstName $lastName";
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person('Rizki Adi'),
            new Person('Asep AC')
        ], $result->all());
    }

    public function testMapToGroup()
    {
        $collection = collect([
            ['name' => 'Rizki', 'dept' => 'IT'],
            ['name' => 'Adi', 'dept' => 'IT'],
            ['name' => 'Nug', 'dept' => 'HR']
        ]);

        // iterate all data and sending each data to function
        // funstion must return single key-value array to group new collection
        $result = $collection->mapToGroups(function ($item) {
            return [
                $item['dept'] => $item['name']
            ];
        });
        
        $this->assertEquals([
            'IT' => collect(['Rizki', 'Adi']),
            'HR' => collect(['Nug'])
        ], $result->all());
    }
}
