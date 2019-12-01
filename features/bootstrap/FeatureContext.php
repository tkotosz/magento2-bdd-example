<?php

use Behat\Behat\Context\Context;
use Inviqa\HelloWorld\Service\HelloService;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /** @var HelloService */
    private $helloWorldService;

    /** @var string|null */
    private $userName = null;

    /** @var string|null */
    private $message = null;

    public function __construct()
    {
        $this->helloWorldService = new HelloService();
    }

    /**
     * @Given the user is unknown
     */
    public function theUserIsUnknown()
    {
        $this->userName = null;
    }

    /**
     * @Given the user is called :name
     */
    public function theUserIsCalled(string $name)
    {
        $this->userName = $name;
    }

    /**
     * @When the user runs the hello world service
     */
    public function theUserRunsTheHelloWorldService()
    {
        if ($this->userName === null) {
            $this->message = $this->helloWorldService->sayHello();
        } else {
            $this->message = $this->helloWorldService->sayHelloToSomeone($this->userName);
        }
    }

    /**
     * @Then a general hello message is shown
     */
    public function aGeneralHelloMessageIsShown()
    {
        assert("Hello World!" === $this->message);
    }

    /**
     * @Then a hello message for :name is shown
     */
    public function aHelloMessageForIsShown(string $name)
    {
        assert(sprintf("Hello %s!", $name) === $this->message);
    }
}
