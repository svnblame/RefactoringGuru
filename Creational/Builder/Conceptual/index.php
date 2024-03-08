<?php

namespace RefactoringGuru\Creational\Builder\Conceptual;

/**
 * Builder is a creational design pattern, which allows constructing complex
 * objects step by step.
 *
 * Unlike other creational patterns, Builder doesn't require products to have a
 * common interface. That makes it possible to produce different products using
 * the same construction process.
 *
 * Usage examples: The Builder pattern is a well-known pattern in the PHP world. It's
 * especially useful when you need to create an object with lots of possible
 * configuration options.
 *
 * Identification: The Builder pattern can be recognized in a class, which has a single
 * creation method and several methods to configure the resulting object. Builder methods
 * often support chaining (for example, someBuilder->setValueA(1)->setValueB(2)->create()).
 *
 */

/**
 * The Buildler interface specifies methods for creating the differenct parts of
 * the Product objects.
 */
interface Builder
{
    public function producePartA(): void;
    public function producePartB(): void;
    public function producePartC(): void;
}

/**
 * The Concreate Builder classes follow the Builder interface and provide
 * specific implementations of the building steps. Your program may have several
 * variations of Builders, implemented differently.
 */
class ConcreateBuilder1 implements Builder
{
    private $product;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used to further assemble.
     */
    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->product = new Product1();
    }

    /**
     * All production steps work with the same product instance.
     */
    public function producePartA(): void
    {
        $this->product->parts[] = "PartA1";
    }

    public function producePartB(): void
    {
        $this->product->parts[] = "PartB1";
    }

    public function producePartC(): void
    {
        $this->product->parts[] = "PartC1";
    }

    /**
     * Concrete Builders are supposed to provide their own methods for
     * retrieving results. That's because various types of builders may create
     * entirely different products that don't follow the same interface.
     * Therefore, such methods cannot be declared in the base Builder interface
     * ( at least in a statically typed programming language ). Note that PHP is a
     * dynamically typed language and this method CAN be in the base interface.
     * However, we won't declare it there for the sake of clarity.
     *
     * Usually, after returning the end result to the client, a builder instance
     * is expected to be ready to start producing another product. That's why
     * it's a usual practice to call the reset method at the end of the `getProduct`
     * method body. However, this behavior is not mandatory, and
     * you can make your builders wait for an explicit reset call from the
     * client code before disposing of the previous result.
     */
    public function getProduct(): Product1
    {
        $result = $this->product;
        $this->reset();

        return $result;
    }
}

/**
 * It makes sense to use the Builder pattern only when your product's are quite
 * complex and require extensive configuration.
 *
 * Unlike in other creational patterns, different concrete builders can produce
 * unrelated products. In other words, results of various builders may not
 * always follow the same interface.
 */
class Product1
{
    public $parts = [];

    public function listParts(): void
    {
        echo "Product parts: " . implode(', ', $this->parts) . PHP_EOL . PHP_EOL;
    }
}

/**
 * The Director is only responsible for executing the building steps in a
 * particular sequence. It is helpful when producing products according to a
 * specific order or configuration. Strictly speakin, the Director class is
 * optional, since the client can control builders directly.
 */
class Director
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * The Director works with any builder instance that the client code passes
     * to it. This way, the client code may alter the final type of the newly
     * assembled product.
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * The Director can construct several product variations using the same
     * building steps.
     */
    public function buildMinimalViableProduct(): void
    {
        $this->builder->producePartA();
    }

    public function buildFullFeaturedProduct(): void
    {
        $this->builder->producePartA();
        $this->builder->producePartB();
        $this->builder->producePartC();
    }
}

/**
 * The client code creates a builder object, passes it to the director and then
 * initiates the construction process. The end result is retrieved from the
 * builder object.
 */
function clientCode(Director $director)
{
    $builder = new ConcreateBuilder1();
    $director->setBuilder($builder);

    echo "Standard basic product:" . PHP_EOL;
    $director->buildMinimalViableProduct();
    $builder->getProduct()->listParts();

    echo "Standard full featured product:" . PHP_EOL;
    $director->buildFullFeaturedProduct();
    $builder->getProduct()->listParts();

    // Remmember, the Builder pattern can be used without a Director class.
    echo "Custom product:" . PHP_EOL;
    $builder->producePartA();
    $builder->producePartC();
    $builder->getProduct()->listParts();
}

$director = new Director();
clientCode($director);
