<?php 

namespace RefactoringGuru\Structural\Facade\Conceptual;

/**
 * The Facade class provides a simple interface to the complex logic of one or
 * several subsystems. The Facade delegates the client requests to the
 * appropriate objects within the subsystem. The Facade is also responsible for
 * managing their lifecycle. All of this shields the client from the undesired 
 * complexity of the subsystem.
 */
class Facade
{
    protected $subsystem1;
    protected $subsystem2;

    /**
     * Depending on your application's needs, you can provide the Facade with
     * existing subsystem objects or force the Facade to create them on its own
     */
    public function __construct(Subsystem1 $subsystem1 = null, Subsystem2 $subsystem2 = null)
    {
        $this->subsystem1 = $subsystem1 ?: new Subsystem1();
        $this->subsystem2 = $subsystem2 ?: new Subsystem2();
    }

    /**
     * The Facade's methods are convenient shortcuts to the sophisticated
     * functionality of the subsystems. However, clients get only to a fraction
     * of a subsystem's capabilitiees.
     */
    public function operation(): string
    {
        $result = "Facade initializes subsystems:" . PHP_EOL;
        $result .= $this->subsystem1->operation1();
        $result .= $this->subsystem2->operation1();
        $result .= "Facade orders subsystems to perform the action:" . PHP_EOL;
        $result .= $this->subsystem1->operationN();
        $result .= $this->subsystem2->operationZ();

        return $result;
    }
}

/**
 * The Subsystem can accept requests either from the facade or client directly.
 * In any case, to the Subsystem, the Facade is yet another client, and it's not
 * a part of the Subsystem.
 */
class Subsystem1
{
    public function operation1(): string 
    {
        return "Subsystem1: Ready!" . PHP_EOL;
    }

    public function operationN(): string 
    {
        return "Subsystem1: Go!" . PHP_EOL;
    }
}

/**
 * Some Facades can work with multiple subsystems at the same time.
 */
class Subsystem2
{
    public function operation1(): string 
    {
        return "Subsystem2: Get ready!" . PHP_EOL;
    }

    public function operationZ(): string 
    {
        return "Subsystem2: Fire!" . PHP_EOL;
    }
}

/**
 * The client code works with complex systems through a simple interface
 * provided by the Facade. When a facade manages the lifecycle of the subsystem,
 * the client might not even know about the existence of the subsystem. This
 * approach lets yoou keep the complexity under control.
 */
function clientCode(Facade $facade)
{
    // ...
    echo $facade->operation();
    // ...
}

/**
 * The client code may have some of the subsystem's objects already created. In
 * this case, it might be worthwhile to initialize the Facade with these objects
 * instead of letting the Facade create new instances.
 */
$subsystem1 = new Subsystem1();
$subsystem2 = new Subsystem2();
$facade = new Facade($subsystem1, $subsystem2);
clientCode($facade);
