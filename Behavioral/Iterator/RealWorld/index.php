<?php

/**
 * Since PHP already has a build-in Iterator interface, which provides convenient
 * integration with foreach loops, it's very easy to create your own iterators for
 * traversing almost every imaginable data structure.
 * 
 * This example of the Iterator pattern provides easy access to CSV files.
 */

namespace RefactoringGuru\Behavioral\Iterator\RealWorld;

use Iterator;

 /**
  * CSV File Iterator
  * @author Gene Kelley
  */
class CsvIterator implements \Iterator
{
    const ROW_SIZE = 4096;

    /**
     * The pointer to the CSV file.
     * 
     * @var resource
     */
    protected $filePointer = null;

    /**
     * The current element, which is returned on each iteration.
     * 
     * @var array
     */
    protected $currentElement = null;

    /**
     * The row counter.
     * 
     * @var int
     */
    protected $rowCounter = null;

    /**
     * The delimiter for the CSV file.
     * 
     * @var string
     */
    protected $delimiter = null;

    /**
     * The constructor tries to open the CSV file. It throws an exception on
     * failure.
     */
    public function __construct($file, $delimiter = ',')
    {
        try {
            $cleanFile = 
            $this->filePointer = fopen($file, 'rb');
            $this->delimiter = $delimiter;
        } catch (\Exception $e) {
            throw new \Exception('The file "' . $file . '" cannot be read.');
        }
    }

    /**
     * This method resets the file pointer.
     */
    #[\ReturnTypeWillChange]
    public function rewind(): void
    {
        $this->rowCounter = 0;
        rewind($this->filePointer);
    }

    /**
     * This method reeturns the current CSV row as a 2-dimensional array.
     * 
     * @return array|bool The current CSV row as a 2-dimensional array if not empty, bool false otherwise.
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        $this->currentElement = fgetcsv($this->filePointer, self::ROW_SIZE, $this->delimiter);
        $this->rowCounter++;

        if (!empty($this->currentElement[$this->key()])) {
            return $this->currentElement;
        }

        return false;
    }

    /**
     * This method returns the current row number.
     * 
     * @return int The current row number.
     */
    #[\ReturnTypeWillChange]
    public function key(): int
    {
        return $this->rowCounter;
    }

    /**
     * This method checks if the end of file has been reached.
     * 
     * @return bool Return true on EOF reached, false otherwise.
     */
    #[\ReturnTypeWillChange]
    public function next(): bool
    {
        if (is_resource($this->filePointer)) {
            return !feof($this->filePointer);
        }

        return false;
    }

    /**
     * This method checks if the next row is a valid row.
     * 
     * @return bool If the next row is a valid row.
     */
    #[\ReturnTypeWillChange]
    public function valid(): bool
    {
        if (!$this->next()) {
            if (is_resource($this->filePointer)) {
                fclose($this->filePointer);
            }

            return false;
        }

        return true;
    }
}

/**
 * The client code.
 */
$csv = new CsvIterator(__DIR__ . '/cats.csv');

foreach ($csv as $key => $row) {
    // if (!empty($row[$key])) {
    //     print_r($row);
    // }
    print_r($row);
}
