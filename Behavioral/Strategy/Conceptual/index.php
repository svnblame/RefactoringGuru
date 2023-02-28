<?php 

namespace RefactoringGuru\Behavioral\Strategy\Conceptual;

/**
* The Context defines the interface of interest to clients.
*/
class Context
{
    /**
    * @var Strategy The Context maintains a reference to one of the Strategy
    * objects. The Context does not know the concrete class of a strategy. It
    * should work with all strategies via the Strategy interface.
    */
    private $strategy;

    /**
    * Usually, the Context accepts a strategy through the constructor, but also 
    * provides a setter to change it at runtime.
    */
    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
    * Usually, the Context allows replacing a Strategy object at runtime.
    */
    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * The Context delegates some work to the Strategy object instead of
     * implementing multiple versions of the algorithm on its own.
     */
    public function doSomeBusinessLogic(): void 
    {
        // ...

        echo "Context: Sorting data using the strategy (not sure how it'll do it)" . PHP_EOL;
        $result = $this->strategy->doAlgorithm(["a", "b", "c", "d", "e"]);
        echo implode(",", $result) . PHP_EOL;

        // ...
    }
}

/**
 * The Strategy interface declares operations common to all supported versions
 * of some algorithm.
 * 
 * The Context uses this interface to call the algorithm defined by Concrete
 * Strategies.
 */
interface Strategy 
{
    public function doAlgorithm(array $data): array;
}

/**
 * Concrete Strategies implement the algorithm while following the base Strategy
 * interface. The interface makes them interchangeable in the Context.
 * 
 * @return array
 */
class ConcreteStrategyA implements Strategy 
{
    public function doAlgorithm(array $data): array 
    {
        sort($data);

        return $data;
    }
}

class ConcreteStrategyB implements Strategy 
{
    public function doAlgorithm(array $data): array
    {
        rsort($data);

        return $data;
    }
}

/**
 * The client code picks a concrete strategy and passes it to the context. The
 * client should be aware of the differences between strategies in order to make
 * the right choice.
 */
$context = new Context(new ConcreteStrategyA());
echo "Client: Strategy is set to normal sorting." . PHP_EOL;
$context->doSomeBusinessLogic();

echo PHP_EOL;

echo "Client: Strategy is set to reverse sorting." . PHP_EOL;
$context->setStrategy(new ConcreteStrategyB());
$context->doSomeBusinessLogic();