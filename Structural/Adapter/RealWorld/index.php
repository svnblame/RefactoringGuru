<?php 

namespace RefactoringGuru\Structural\Adapter\RealWorld;

/**
 * The Target interface represents the interface that your application's classes
 * already follow.
 */
interface Notification
{
    public function send(string $title, string $message);
}

/**
 * Here's an example of the existing class that follows the Target interface.
 * 
 * The truth is that many real apps may not have this interface clearly defined.
 * If you're in that boat, your best bet would be to extend the Adapter from one
 * of your application's existing classes. If that's awkward (for instance, 
 * SlackNotification doesn't feel like a subclass of EmailNotification), then
 * extracting an interface should be your first step.
 */
class EmailNotification implements Notification
{
    private $adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function send(string $title, string $message): void
    {
        mail($this->adminEmail, $title, $message);
        echo "Sent email with title `$title` to `{$this->adminEmail}` that says `$message`." . PHP_EOL;
    }
}

/**
 * The Adaptee is some useful class, incompatible with the Target interface. You
 * can't just go in and change the code of the class to follow the Target
 * interface, since the code might be provided by a 3rd-party library.
 */
class SlackApi
{
    private $login, $apiKey;

    public function logIn(): void
    {
        // Send authentication request to Slack web service.
        echo "Logged in to Slack account '{$this->login}'." . PHP_EOL;
    }

    public function sendMessage(string $chatId, string $message): void
    {
        // Send message post request to Slack web service.
        echo "Posted following message into the '$chatId' chat: '$message'." . PHP_EOL;
    }
}

/**
 * The Adapter is a clas that links the Target interface and the Adaptee class.
 * In this case, it allows the application to send notifications using Slack
 * API.
 */
class SlackNotification implements Notification
{
    private $slack, $chatId;

    public function __construct(SlackApi $slack, string $chatId)
    {
       $this->slack = $slack;
       $this->chatId = $chatId; 
    }

    /**
     * An Adapter is not only capable of adapting interfaces, but it can also
     * convert incoming data to the format by the Adaptee.
     */
    public function send(string $title, string $message): void
    {
        $slackMessage = "#" . $title . "# " . strip_tags($message);
        $this->slack->logIn();
        $this->slack->sendMessage($this->chatId, $slackMessage);
    } 
}

/**
 * The client code can work with any class that follows the Target interface.
 */
function clientCode(Notification $notification)
{
    // ...

    echo $notification->send("Website is down!",
        "<strong style='color:red;font-size: 50px;'>Alert!</strong> " . 
        "Our website is not responding. Call admins and bring it back up!");
    
    // ...
}

echo "Client code is designed correctly and works with email notifications: " . PHP_EOL;
$notification = new EmailNotification('developer@example.com');
clientCode($notification);
echo PHP_EOL . PHP_EOL;

echo "The same client code can work with other classes via adapter: " . PHP_EOL;
$slackApi = new SlackApi('example.com', 'XXXXXXXX');
$notification = new SlackNotification($slackApi, 'Example.com Developers');
clientCode($notification);
