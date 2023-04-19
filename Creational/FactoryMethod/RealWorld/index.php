<?php 
namespace RefactoringGuru\FactoryMethod\RealWorld;

/** The Creator declares a factory mthod that can be uses as a substitute for
 * the direct constructor calls of products, for instance:
 * 
 * - Before: $p = new FacebookConnector();
 * - After:  $p = $this->getSocialNetwork();
 * 
 * This allows changing the type of the product being crated by
 * SocialNetworkPoster's subclasses.
 */
abstract class SocialNetworkPoster 
{
    /**
     * The actual factory method. Note that it returns the abstract connector.
     * This lets subclasses return any concrete connectors without breaking the
     * superclass' contract.
     */
    abstract public function getSocialNetwork(): SocialNetworkConnector;

    /**
     * When the factory method is used inside the Crator's business logic, the
     * subclasses amy alter the logic indirectly by returning different types of
     * the connector from the factory method.
     */
    public function post($content): void 
    {
        // Call the factory method to create a Product object...
        $network = $this->getSocialNetwork();
        // ... then use it as you will.
        $network->logIn();
        $network->createPost($content);
        $network->logout();
    }
}

/** 
 * This Concrete Creator supports Facebook. Remember that this class also
 * inherits the `post` method from the parent class. Concrete Creators are the
 * lasses that the Client actually uses.
*/ 
class FacebookPoster extends SocialNetworkPoster 
{
    private $login, $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector 
    {
        return new FacebookConnector($this->login, $this->password);
    }
}

/**
 * This Concrete Creator supports LinkedIn.
 */
class LinkedInPoster extends SocialNetworkPoster 
{
    private $email, $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getSocialNetwork(): SocialNetworkConnector
    {
        return new LinkedInConnector($this->email, $this->password);
    }
}

/**
 * The Product interface declares behaviors of various types of products.
 */
interface SocialNetworkConnector
{
    public function logIn(): void;

    public function logOut(): void;

    public function createPost($content): void;
}

/**
 * This Concrete Product implements the Facebook API.
 */
class FacebookConnector implements SocialNetworkConnector 
{
    private $login, $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function logIn(): void 
    {
        echo "Facebook login. User : $this->login . Password: " . $this->password . PHP_EOL;
    }

    public function logOut(): void 
    {
        echo "Facebook logout. User: $this->login" . PHP_EOL;
    }

    public function createPost($content): void 
    {
        echo "Content: " . $content . PHP_EOL;
    }
}

/**
 * This Concrete Product implements the LinkedIn API.
 */
class LinkedInConnector implements SocialNetworkConnector 
{
    private $email, $password;

    public function __construct(string $email, string $password)
    {
       $this->email = $email;
       $this->password = $password; 
    }

    public function logIn(): void 
    {
        echo "LinkedIn login. User: $this->email , Password: $this->password" . PHP_EOL;
    }

    public function logOut(): void 
    {
        echo "LinkedIn logout. User: $this->email" . PHP_EOL;
    }

    public function createPost($content): void 
    {
        echo "Content:" . PHP_EOL . $content . PHP_EOL;
    }
}

/**
 * The client code can work with any subclass of SocialNetworkPoster since it
 * doesn't depend on concrete classes.
 */
function clientCode(SocialNetworkPoster $creator)
{
    // ...
    $creator->post("Hello world!");
    $creator->post("I had a large hamburger this morning!");
    // ...
}

/**
 * During the initializatin phase, the app can decide which social network it
 * wants to work with, create an object of the proper subclass, and pass it to 
 * the client code.
 */
echo "Testing FacebookPoster:" . PHP_EOL;
clientCode(new FacebookPoster("john_smith", "********"));
echo PHP_EOL . PHP_EOL;

echo "Testing LinkedInPoster:" . PHP_EOL;
clientCode(new LinkedInPoster("john_smith@example.com", "********"));
echo PHP_EOL . PHP_EOL;


