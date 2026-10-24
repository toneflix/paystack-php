<?php

namespace Toneflix\Paystack;

class MetadataBuilder
{
    /** @var array<string, mixed> */
    private array $meta = [];

    public static bool $autoSnakeCase = true;

    /**
     * Add a custom field, shown on the Paystack checkout page.
     */
    public function withCustomField(string $title, string $value): static
    {
        $this->meta['custom_fields'] ??= [];
        $this->meta['custom_fields'][] = [
            'display_name' => $title,
            'variable_name' => $title,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Magic setter for any other top-level metadata key, e.g.
     * withCancelAction($url) sets "cancel_action" and
     * withCustomFilters($filters) sets "custom_filters".
     */
    public function __call(string $method, array $args): static
    {
        if (str_starts_with($method, 'with') && $method !== 'with') {
            $name = substr($method, 4);

            if (static::$autoSnakeCase) {
                $name = $this->toSnakeCase($name);
            }

            if ($name === 'custom_fields') {
                throw new \BadMethodCallException('Use withCustomField() to add custom fields.');
            }

            $this->meta[$name] = $args[0] ?? null;

            return $this;
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $method));
    }

    private function toSnakeCase(string $input): string
    {
        preg_match_all('/([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)/', $input, $matches);

        return implode('_', array_map(
            fn (string $match) => $match === strtoupper($match) ? strtolower($match) : lcfirst($match),
            $matches[0]
        ));
    }

    public function build(): string
    {
        return json_encode($this->meta);
    }
}
