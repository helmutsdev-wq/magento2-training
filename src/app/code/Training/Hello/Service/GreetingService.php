<?php
namespace Training\Hello\Service;

class GreetingService
{
    public function getRandomQuote(): string
    {
        $quotes = [
            "Hello, Magento Ninja! The time is " . date('H:i:s'),
            "Welcome to the world of Magento!",
            "Keep coding and have fun!"
        ];
        return $quotes[array_rand($quotes)];
    }
}