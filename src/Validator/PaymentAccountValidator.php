<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PaymentAccountValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PaymentAccount) {
            throw new UnexpectedTypeException($constraint, PaymentAccount::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->validatePhone($value) && !$this->validateCard($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

    private function validatePhone($value): bool
    {
        return preg_match('/^[+]?[1-9][0-9]{9,14}$/', $value);
    }

    private function validateCard($value): bool
    {
        return $this->luhnCheck($value);
    }

    private function luhnCheck($number): bool
    {
        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number = preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $numberLength = strlen($number);
        $parity        = $numberLength % 2;

        // Loop through each digit and do the maths
        $total = 0;
        for ($i = 0; $i < $numberLength; $i++) {
            $digit = $number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit *= 2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            // Total up the digits
            $total += $digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return $total % 10 == 0;
    }
}