<?php

namespace RefactoringGuru\Creational\FactoryMethod\Conceptual;

use Symfony\Component\Process\Exception\ProcessTimedOutException;

/**
 * The Creator class declares the factory method that is supported to return
 * an object of a Product class. The Crator's subclasses usually provide the
 * implementation of this method.
 */
abstract class Creator
{
    /**
     * Note that the Creator may also provide some default implementation
     * of the factory method.
     */
    abstract public function factoryMethod(): Product;

    /**
     * Also note that, despite its name, the Creator's primary responsibility
     * is not creating products. Usually, it contains some core business logic
     * that relies on Product objects, returned by the factory method. 
     * Subclasses can indirectly change that business logic by overriding
     * the factory method and returning a different type of product from it.
     */
    public function someOperation(): string
    {
        // Call the factory method to create a Product object.
        $product = $this->factoryMethod();
        // Now, use the product.
        $result = "Creator: The same creator's code has just worked with " . 
            $product->operation();
        
        return $result;
    }
}

/**
 * Concrete Creators override the factory method in order to change the
 * resulting product's type.
 */
class ConcreteCreator1 extends Creator
{
    /**
     * Note that the signature of the method still uses the abstract product
     * type, even though the concrete product is actually returned from the
     * method. This way the Creator can stay independent of concrete product
     * classes.
     */
    public function factoryMethod(): Product 
    {
        return new ConcreteProduct1();
    }
}

class ConcreteCreator2 extends Creator 
{
    public function factoryMethod(): Product 
    {
        return new ConcreteProduct2();
    }
}

/**
 * The Product interface declares the operations that all concrete products
 * must impement.
 */
interface Product 
{
    public function operation(): string;
}

/**
 * Concrete Products provide various implementations of the Product interface.
 */
class ConcreteProduct1 implements Product
{
    public function operation(): string
    {
        return "{Result of the ConcreteProduct1}";
    }
}

class ConcreteProduct2 implements Product
{
    public function operation(): string
    {
        return "{Result of the ConcreteProduct2}";
    }
}

/**
 * The client code works with an instance of a concrete creator, albeit through
 * it base interface. As long as the client keeps working with the creator via
 * the base interface, you can pass any creator's subclass.
 */
function clientCode(Creator $creator)
{
    // ...
    echo "Client: I'm not aware of the creator's class, but it still works." . PHP_EOL . $creator->someOperation();
    // ...
}

/**
 * The Application picks a creator's type depending on the configuration or
 * environment.
 */
echo "App: Launched with the ConcreteCreator1." . PHP_EOL;
clientCode(new ConcreteCreator1());
echo PHP_EOL . PHP_EOL;

echo "App: Launched with the ConcreteCreator2." . PHP_EOL;
clientCode(new ConcreteCreator2());
