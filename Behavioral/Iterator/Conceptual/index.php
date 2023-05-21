<?php

/**
 * This example illustrates the structure of the Iterator design pattern and focuses on
 * the following questions:
 * 
 * - What classes does it consist of?
 * - What roles do these classes play?
 * - In what way are the elements of the pattern related?
 * 
 * After learning about the pattern's structure it'll be easier to grasp the
 * real world PHP use cases.
 */

namespace RefactoringGuru\Behavioral\Iterator\Conceptual;

use \Iterator;

/**
 * Concrete Iterators implement various traversal algorithms. These classes
 * store the current traversal position at all times.
 */

class AlphabeticalOrderIterator implements \Iterator
{
    /**
     * @var WordsCollection
     */
    private $collection;

    /**
     * @var int Stores the current traversal position. An iterator may have a
     * lot of other fields for storing iteration state, especially when it is
     * supposed to work with a particular kind of collection.
     */
    private $position = 0;

    /**
     * @var bool This variable indicates the traversal direction.
     */
    private $reverse = false;

    public function __construct($collection, $reverse = false)
    {
        $this->collection = $collection;
        $this->reverse = $reverse;
    }
    
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = $this->reverse ? count($this->collection->getItems()) -1 : 0;
    }

    #[\ReturnTypeWillChange] 
    public function current()
    {
        return $this->collection->getItems()[$this->position];
    }

    #[\ReturnTypeWillChange] 
    public function key()
    {
        return $this->position;
    }

    #[\ReturnTypeWillChange] 
    public function next()
    {
        $this->position = $this->position + ($this->reverse ? -1 : 1);
    }

    #[\ReturnTypeWillChange] 
    public function valid()
    {
        return isset($this->collection->getItems()[$this->position]);
    }
}

/**
 * Concrete Collections provide one or serveral methods for retrieving fresh
 * iterator instances, compatible with the collection class.
 */
class WordsCollection implements \IteratorAggregate
{
    private $items = [];

    public function getItems()
    {
        return $this->items;
    }

    public function addItem($item)
    {
        $this->items[] = $item;
    }

    public function getIterator(): Iterator
    {
        return new AlphabeticalOrderIterator($this);
    }

    public function getReverseIterator(): Iterator
    {
        return new AlphabeticalOrderIterator($this, true);
    }

}

/**
 * The client code may or may not know about the Concrete Iterator or Collection
 * classes, depending on the level of indirection you want to keep in your
 * program.
 */
$collection = new WordsCollection();
$collection->addItem('First');
$collection->addItem('Second');
$collection->addItem('Third');

echo 'Straight traversal:' . PHP_EOL;
foreach ($collection->getIterator() as $item) {
    echo $item . PHP_EOL;
}

echo PHP_EOL;

echo 'Reverse traversal:' . PHP_EOL;
foreach ($collection->getReverseIterator() as $item) {
    echo $item . PHP_EOL;
}