<?php
declare(strict_types=1);

namespace App\Utils;

final class Validator
{
    /** @var array<string,string> */
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    private function value(string $field): string
    {
        $v = $this->data[$field] ?? '';
        return is_string($v) ? $v : '';
    }

    private function fail(string $field, string $message): void
    {
        $this->errors[$field] ??= $message; // on garde la première erreur du champ
    }

    public function required(string $field, string $label): static
    {
        if (trim($this->value($field)) === '') {
            $this->fail($field, "$label est obligatoire.");
        }
        return $this;
    }

    public function length(string $field, string $label, int $min, int $max): static
    {
        $v = $this->value($field);
        $n = mb_strlen($v);
        if ($v !== '' && ($n < $min || $n > $max)) {
            $this->fail($field, "$label doit contenir entre $min et $max caractères.");
        }
        return $this;
    }

    public function email(string $field, string $label): static
    {
        $v = $this->value($field);
        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            $this->fail($field, "$label n'est pas valide.");
        }
        return $this;
    }

    public function matches(string $field, string $pattern, string $message): static
    {
        $v = $this->value($field);
        if ($v !== '' && !preg_match($pattern, $v)) {
            $this->fail($field, $message);
        }
        return $this;
    }

    public function same(string $field, string $other, string $message): static
    {
        if ($this->value($field) !== $this->value($other)) {
            $this->fail($field, $message);
        }
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string,string> */
    public function errors(): array
    {
        return $this->errors;
    }
}
