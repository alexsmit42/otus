<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ManageCurrencyDTO
{
    #[Assert\NotBlank]
    #[Assert\Currency]
    private string $iso = '';

    #[Assert\NotBlank]
    #[Assert\Positive]
    private float $rate = 0;

    public function __construct(
    ) {
    }

    public function fromRequest(Request $request): self {
        $params = $request->getPayload();

        $this->iso  = $params->get('iso');
        $this->rate = $params->get('rate');

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    public function getIso(): string
    {
        return $this->iso;
    }

    public function setIso(string $iso): void
    {
        $this->iso = $iso;
    }
}