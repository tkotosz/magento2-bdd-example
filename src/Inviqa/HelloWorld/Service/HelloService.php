<?php

declare(strict_types=1);

namespace Inviqa\HelloWorld\Service;

final class HelloService
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
