<?php

namespace spec\Inviqa\HelloWorld\Service;

use Inviqa\HelloWorld\Service\HelloService;
use PhpSpec\ObjectBehavior;

class HelloServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(HelloService::class);
    }

    function it_says_hello_world()
    {
        $this->sayHello()->shouldReturn("Hello World!");
    }

    function it_says_hello_to_bob()
    {
        $this->sayHelloToSomeone('Bob')->shouldReturn("Hello Bob!");
    }
}
