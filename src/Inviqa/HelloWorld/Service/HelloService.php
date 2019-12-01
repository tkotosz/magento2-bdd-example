<?php

namespace Inviqa\HelloWorld\Service;

class HelloService
{
    public function sayHello(): string
    {
        return 'Hello World!';
    }

    public function sayHelloToSomeone(string $name): string
    {
        return sprintf('Hello %s!', $name);
    }
}
