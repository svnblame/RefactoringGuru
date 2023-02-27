<?php

/**
 * Strategy is a behavioral design pattern that lets you define a family of
 * algorithms, put each of them into a separate class, and make their objects
 * interchangeable.
 */

namespace RefactoringGuru\Strategy\RealWorld;

use AllowDynamicProperties;

/**
 * This is the router and controller of our application. Upon receiving a
 * request, this class desides what behavior should be executed. When the app
 * receives a payment request, the OrderController class also decides which
 * payment method it should use to process the request. Thus, the class acts as
 * the Context and the Client at the same time.
 */
class OrderController 
{
    /**
     * Handle POST requests.
     * 
     * @param $url
     * @param $data
     * @throws \Exception
     */
    public function post(string $url, array $data)
    {
        echo "Controller: POST request to $url with " . json_encode($data) . PHP_EOL;

        $path = parse_url($url, PHP_URL_PATH);

        if (preg_match('#^/orders?$#', $path, $matches)) {
            $this->postNewOrder($data);
        } else {
            echo "Controller: 404 Not Found" . PHP_EOL;
        }
    }

    public function get(string $url): void
    {
        echo "Controller: GET request to $url" . PHP_EOL;
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        $query = is_null($query) ? "" : $query;
        parse_str($query, $data);

        if (preg_match('#^/orders?$#', $path, $matches)) {
            $this->getAllOrders();
        } elseif (preg_match('#^/order/([0-9]+?)/payment/([a-z]+?)(/return)?$#', $path, $matches)) {
            $order = Order::get($matches[1]);

            // The payment method (strategy) is selected according to the value
            // passed along with the request.
            $paymentMethod = PaymentFactory::getPaymentMethod($matches[2]);

            if (!isset($matches[3])) {
                $this->getPayment($paymentMethod, $order, $data);
            } else {
                $this->getPaymentReturn($paymentMethod, $order, $data);
            }
        } else {
            echo "Controller: 404 Not Found" . PHP_EOL;
        }
    }

    /**
     * POST /order {data}
     */
    public function postNewOrder(array $data): void
    {
        $order = new Order($data);
        echo "Controller: Created the order #{$order->id}." . PHP_EOL;
    }

    /**
     * GET /orders
     */
    public function getAllOrders(): void
    {
        echo "Controller: Here's all orders:" . PHP_EOL;
        foreach (Order::get() as $order) {
            echo json_encode($order, JSON_PRETTY_PRINT) . PHP_EOL;
        }
    }

    /**
     * GET /order/123/payment/XX
     */
    public function getPayment(PaymentMethod $method, Order $order, array $data): void
    {
        // The actual work is delegated to the payment method object.
        $form = $method->getPaymentForm($order);
        echo "Controller: here's the payment form:" . PHP_EOL;
        echo $form . PHP_EOL;
    }

    /**
     * GET /order/123/payment/XXX/return?key=AJHKSJHJ3423&success=true
     */
    public function getPaymentReturn(PaymentMethod $method, Order $order, array $data): void
    {
        try {
            // Another type of work delegated to the payment method.
            if ($method->validateReturn($order, $data)) {
                echo "Controller: Thanks for your order!" . PHP_EOL;
                $order->complete();
            }
        } catch (\Exception $e) {
            echo "Controller: got an exception (" . $e->getMessage() . ")" . PHP_EOL;
        }
    }
}

/**
 * A simplified representation of the Order class.
 */
#[AllowDynamicProperties]
class Order 
{
    /**
     * For the sake of simplicity, we'll store all created orders here... 
     * 
     * @var array
     */
    private static $orders = [];

    public $id;
    public $status;

    /**
     * The Order constructor assigns the values of the order's fields. To keep
     * things simple, there is no validation whatsoever.
     * 
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
       $this->id = count(static::$orders);
       $this->status = "new";
       foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
       }
       static::$orders[$this->id] = $this;
    }

    /**
     * Provide access to orders from here.
     * 
     * @param int $orderId
     * @return mixed
     */
    public static function get(int $orderId = null)
    {
        if ($orderId === null) {
            return static::$orders;
        } else {
            return static::$orders[$orderId];
        }
    }

    /**
     * The method to call when an order gets paid.
     */
    public function complete(): void
    {
        $this->status = "completed";
        echo "Order: #{$this->id} is now {$this->status}." . PHP_EOL;
    }
}

/**
 * This class helps to produce a proper strategy object for handling a payment.
 */
class PaymentFactory 
{
    /**
     * Get a payment method by its ID.
     * 
     * @param $id
     * @return PaymentMethod
     * @throws \Exception
     */
    public static function getPaymentMethod(string $id): PaymentMethod 
    {
        switch ($id) {
            case "cc":
                return new CreditCardPayment();
            case "paypal":
                return new PayPalPayment();
            default:
                throw new \Exception("Unknown Payment Method");
        }
    }
}

/**
 * The Strategy interface describes how a client can use various Concrete
 * Strategies.
 * 
 * Note that in most examples you can find on the Web, strategies tend to do
 * some tiny thing within on method. However, in reality, your strategies can
 * be much more robust (by having several methods, for example).
 */
interface PaymentMethod 
{
    public function getPaymentForm(Order $order): string;
    public function validateReturn(Order $order, array $data): bool;
}

/**
 * This Concrete Strategy provides a payment form and validates returns for
 * credit card payments.
 */
class CreditCardPayment implements PaymentMethod 
{
    static private $store_secret_key = "swordfish";

    public function getPaymentForm(Order $order): string
    {
        $returnURL = "https://our-website.com/order/{$order->id}/payment/cc/return";

        return <<<FORM
<form action="https://my-credit-card-processor.com/charge" method="POST">
    <input type="hidden" id="email" value="{$order->email}">
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="text" id="cardholder-name">
    <input type="text" id="credit-card">
    <input type="text" id="expiration-date">
    <input type="text" id="ccv-number">
    <input type="submit" value="Pay">
</form>
FORM;
    }

    public function validateReturn(Order $order, array $data): bool
    {
        echo "CreditCardPayment: ... validating ... " . PHP_EOL;

        if ($data['key'] != md5($order->id . static::$store_secret_key)) {
            throw new \Exception("Payment key is wrong." . PHP_EOL);
        }

        if (!isset($data['success']) || !$data['success'] || $data['success'] == 'false') {
            throw new \Exception("Payment failed." . PHP_EOL);
        }

        // ...

        if (floatval($data['total']) < $order->total) {
            throw new \Exception("Payment amount is wrong." . PHP_EOL);
        }

        echo "Done!" . PHP_EOL;

        return true;
    }
}

/**
 * This Concrete Strategy provides a payment form and validates returns for
 * PayPal payments.
 */
class PayPalPayment implements PaymentMethod
{
    public function getPaymentForm(Order $order): string
    {
        $returnURL = "https://our-website.com/order/{$order->id}/payment/paypal/return";

        return <<<FORM
<form action="https://paypal.com/payment" method="POST">
    <input type="hidden" id="email" value={$order->email}>
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="submit" value="Pay with PayPal">
</form>
FORM;
    }

    public function validateReturn(Order $order, array $data): bool
    {
        echo "PayPalPayment: ... validating ... " . PHP_EOL;

        // ...

        echo "Done!" . PHP_EOL;

        return true;
    }
}

/**
 * The client code.
 */
$controller = new OrderController();

echo "Client: Let's create some orders" . PHP_EOL;

$controller->post("/orders", [
    "email" => "me@example.com",
    "product" => "ABC Cat Food (XL)",
    "total" => 9.95,
]);

$controller->post("/orders", [
    "email" => "me@example.com",
    "product" => "XYZ Cat Litter (XXL)",
    "total" => 19.95,
]);

echo PHP_EOL . "Client: List my orders, please" . PHP_EOL;

$controller->get("/orders");

echo PHP_EOL . "Client: I'd like to pay for the second, show me the payment form" . PHP_EOL;

$controller->get("/order/1/payment/paypal");

echo PHP_EOL . "Client: ... pushes the Pay button ..." . PHP_EOL;
echo PHP_EOL . "Client: Oh, I'm redirected to the PayPal." . PHP_EOL;
echo PHP_EOL . "Client: ... pays on the PayPal site ..." . PHP_EOL;
echo PHP_EOL . "Client: Alright, I'm back with you, guys." . PHP_EOL;

$controller->get("/order/1/payment/paypal/return?key=c55a3964833a4b0fa4469ea94a057152&success=true&total=19.95");
