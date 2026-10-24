<?php

namespace Toneflix\Paystack\Tests;

use PHPUnit\Framework\TestCase;
use Toneflix\Paystack\MetadataBuilder;

class MetadataBuilderTest extends TestCase
{
    public function testWithCustomFieldAppendsToCustomFields(): void
    {
        $meta = (new MetadataBuilder())
            ->withCustomField('Name', 'Jane Doe')
            ->withCustomField('Phone Number', '08012345678')
            ->build();

        $this->assertSame([
            'custom_fields' => [
                ['display_name' => 'Name', 'variable_name' => 'Name', 'value' => 'Jane Doe'],
                ['display_name' => 'Phone Number', 'variable_name' => 'Phone Number', 'value' => '08012345678'],
            ],
        ], json_decode($meta, true));
    }

    public function testMagicWithMethodsSnakeCaseTheKey(): void
    {
        $meta = (new MetadataBuilder())
            ->withCancelAction('https://example.com/cancel')
            ->withCustomFilters(['recurring' => true])
            ->build();

        $this->assertSame([
            'cancel_action' => 'https://example.com/cancel',
            'custom_filters' => ['recurring' => true],
        ], json_decode($meta, true));
    }

    public function testWithCustomFieldsCannotBeSetThroughTheMagicMethod(): void
    {
        $this->expectException(\BadMethodCallException::class);

        (new MetadataBuilder())->withCustomFields(['foo' => 'bar']);
    }

    public function testUndefinedMethodsThrow(): void
    {
        $this->expectException(\BadMethodCallException::class);

        (new MetadataBuilder())->notAMethod();
    }
}
