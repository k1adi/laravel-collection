<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\LazyCollection;
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

    /** 
     * Zipping
     * grouping of two collection
    */
    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        // zip() is grouping item by index of two collection 
        // to create new collection
        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6])
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        // concat() is merging two of collection
        $this->assertEqualsCanonicalizing([1,2,3,4,5,6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(['name', 'country']);
        $collection2 = collect(['Rizki', 'Indonesia']);
        $collection3 = $collection1->combine($collection2);

        // combine() is grouping item of two collection
        // and make new collection based index-key value
        $this->assertEquals([
            'name' => 'Rizki',
            'country' => 'Indonesia'
        ], $collection3->all());
    }

    /** 
     * Flattening
     * transform 3D collection (nested collection)
     * to flat collection (2D collection/array)
    */
    public function testCollapse()
    {
        $collection = collect([
            [1,2,3],
            [4,5,6],
            [7,8,9]
        ]);

        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1,2,3,4,5,6,7,8,9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            ['name' => 'Rizki', 'hobbies' => ['Coding', 'Writing']],
            ['name' => 'Adi', 'hobbies' => ['Riding', 'Hiking']]
        ]);

        $hobbies = $collection->flatMap(function ($item){
            return $item['hobbies'];
        });

        $this->assertEqualsCanonicalizing(
            ['Coding', 'Writing', 'Riding', 'Hiking'], $hobbies->all()
        );
    }

    // Tranform collection to string
    public function testJoin()
    {
        $collection = collect(['Rizki', 'Adi', 'Budi', 'Asep', 'Mamat']);

        // join() method need 2 param as separator (glue'' and finalGlue'')
        $this->assertEquals('RizkiAdiBudiAsepMamat', $collection->join('')); // as default is ''
        $this->assertEquals('Rizki-Adi-Budi-Asep-Mamat', $collection->join('-'));
        $this->assertEquals('Rizki,Adi,Budi,Asep_Mamat', $collection->join(',', '_'));
        $this->assertEquals('Rizki, Adi, Budi, Asep & Mamat', $collection->join(', ', ' & '));
    }

    public function testFilter()
    {
        $collection = collect([
            'Rizki' => 100,
            'Adi' => 95,
            'Asep' => 90,
            'Joko' => 85
        ]);

        $result = $collection->filter(function($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            'Rizki' => 100,
            'Adi' => 95,
            'Asep' => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        });

        // If the collection is index-based, please consider using a filter.
        // This is because the index will be removed if the data does not yield a result.
        $this->assertEqualsCanonicalizing([
            2,4,6,8,10
        ], $result->all());
    }

    /**
     * Partioning
     * to get two collection that contains result of filter not or result
     * partition() method will make two collection
     * the first collection for result of filter
     * the second collection for nor result of filter
     */

    public function testPartition()
    {
        $collection = collect([
            'Rizki' => 100,
            'Adi' => 95,
            'Asep' => 90,
            'Joko' => 85
        ]);

        [$result1, $result2] = $collection->partition(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            'Rizki' => 100,
            'Adi' => 95,
            'Asep' => 90
        ], $result1->all());

        
        $this->assertEquals([
            'Joko' => 85
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(['Rizki', 'Adi', 'Nugroho']);
        self::assertTrue($collection->contains('Rizki'));
        self::assertTrue($collection->contains(function ($value, $key) {
            return $value == 'Adi';
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            ['name' => 'Rizki', 'dept' => 'IT'],
            ['name' => 'Adi', 'dept' => 'IT'],
            ['name' => 'Budi', 'dept' => 'Finance'],
            ['name' => 'Asep', 'dept' => 'HR'],
        ]);

        $expectedCollection = [
            'IT' => collect([
                ['name' => 'Rizki', 'dept' => 'IT'],
                ['name' => 'Adi', 'dept' => 'IT']
            ]),
            'Finance' => collect([
                ['name' => 'Budi', 'dept' => 'Finance']
            ]),
            'HR' => collect([
                ['name' => 'Asep', 'dept' => 'HR']
            ])
        ];

        $result = $collection->groupBy('dept');
        $this->assertEquals($expectedCollection, $result->all());
        $this->assertEquals($expectedCollection, $collection->groupBy(function ($value, $key) {
            return $value['dept'];
        })->all());
    }

    // To take part of collection by start index and until 
    public function testSlicing()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->slice(3);
        $this->assertEqualsCanonicalizing([4,5,6,7,8,9], $result->all());

        $result = $collection->slice(3,4);
        $this->assertEqualsCanonicalizing([4,5,6,7], $result->all());
    }

    /**
     * Take & Skip
     * same as slicing but with difference method / fuction
     * take(length)
     */

    public function testTake()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->take(5);
        $this->assertEqualsCanonicalizing([1,2,3,4,5], $result->all());

        $result = $collection->take(-2);
        $this->assertEqualsCanonicalizing([8,9], $result->all());

        $result = $collection->takeUntil(function($value, $key) {
            return $value == 4;
        });
        $this->assertEqualsCanonicalizing([1,2,3], $result->all());

        $result = $collection->takeWhile(function($value, $key) {
            return $value <= 6;
        });
        $this->assertEqualsCanonicalizing([1,2,3,4,5,6], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->skip(5);
        $this->assertEqualsCanonicalizing([6,7,8,9], $result->all());

        $result = $collection->skipUntil(function($value, $key) {
            return $value == 4;
        });
        $this->assertEqualsCanonicalizing([4,5,6,7,8,9], $result->all());

        $result = $collection->skipWhile(function($value, $key) {
            return $value <= 6;
        });
        $this->assertEqualsCanonicalizing([7,8,9], $result->all());
    }

    public function testChunk()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->chunk(3);

        $this->assertEqualsCanonicalizing([1,2,3], $result->all()[0]->all());
        $this->assertEqualsCanonicalizing([4,5,6], $result->all()[1]->all());
        $this->assertEqualsCanonicalizing([7,8,9], $result->all()[2]->all());
        $this->assertEqualsCanonicalizing([10], $result->all()[3]->all());
    }

    public function testFirst()
    {
        $collection = collect([1,2,3,4,5]);
        $result = $collection->first();
        $this->assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 3;
        });
        $this->assertEquals(4, $result);
    }

    public function testLast()
    {
        $collection = collect([1,2,3,4,5]);
        $result = $collection->last();
        $this->assertEquals(5, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 3;
        });
        $this->assertEquals(2, $result);
    }

    public function testRandom()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9,10]);
        $result = $collection->random();

        // dd($collection->random(5));
        self::assertTrue(in_array($result, [1,2,3,4,5,6,7,8,9]));
    }

    // Checking existence data in collection
    public function testExistence()
    {
        // isEmpty() : bool
        $empty = collect([]);
        self::assertTrue($empty->isEmpty());

        $collection = collect([1,2,3]);
        self::assertTrue($collection->isNotEmpty());
        self::assertTrue($collection->contains(1));
        self::assertTrue($collection->contains(function($value,$key) {
            return $value == 3;
        }));

        $oneData = collect([1]);
        self::assertTrue($oneData->containsOneItem());
    }

    public function testOrder()
    {
        $collection = collect([1,3,5,2,4]);

        $result = $collection->sort();
        $this->assertEqualsCanonicalizing([1,2,3,4,5], $result->all());
        
        $result = $collection->sortDesc();
        $this->assertEqualsCanonicalizing([5,4,3,2,1], $result->all());

        $result = $collection->reverse();
        $this->assertEqualsCanonicalizing([4,2,5,3,1], $result->all());
    }

    public function testAggregate()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        $result = $collection->min();
        $this->assertEquals(1, $result);

        $result = $collection->max();
        $this->assertEquals(9, $result);

        $result = $collection->avg();
        $this->assertEquals(5, $result);

        $result = $collection->sum();
        $this->assertEquals(45, $result);

        $result = $collection->count();
        $this->assertEquals(9, $result);
    }

    public function testReduce()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);
        $result = $collection->reduce(function($carry, $item) {
            return $carry + $item;
        });

        $this->assertEquals(45, $result);
    }

    public function testLazyCollection()
    {
        $collection = LazyCollection::make(function () {
            $value = 0;
            while (true) {
                yield $value;
                $value++;
            }
        });

        $result = $collection->take(10);
        $this->assertEqualsCanonicalizing([0,1,2,3,4,5,6,7,8,9], $result->all());
    }
}
