<?php

namespace Training\Hello\Api\Data;

interface QuoteInterface
{
    public function getQuoteId(): ?int;
    public function setQuoteId(int $id): QuoteInterface;
    public function getQuoteText(): string;
    public function setQuoteText(string $text): QuoteInterface;
    public function getCreatedAt(): string;
    public function setCreatedAt(string $createdAt): QuoteInterface;
}