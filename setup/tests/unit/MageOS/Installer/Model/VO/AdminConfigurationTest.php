<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\AdminConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for AdminConfiguration VO
 */
class AdminConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): AdminConfiguration
    {
        return new AdminConfiguration(
            firstName: 'John',
            lastName: 'Doe',
            email: 'admin@example.com',
            username: 'admin',
            password: 'Admin123!'
        );
    }

    protected function getSensitiveFields(): array
    {
        return ['password'];
    }

    public function test_it_constructs_with_all_parameters(): void
    {
        $config = new AdminConfiguration(
            firstName: 'Jane',
            lastName: 'Smith',
            email: 'jane@example.com',
            username: 'janeadmin',
            password: 'SecurePass123!'
        );

        $this->assertPropertyEquals($config, 'firstName', 'Jane');
        $this->assertPropertyEquals($config, 'lastName', 'Smith');
        $this->assertPropertyEquals($config, 'email', 'jane@example.com');
        $this->assertPropertyEquals($config, 'username', 'janeadmin');
        $this->assertPropertyEquals($config, 'password', 'SecurePass123!');
    }

    public function test_to_array_excludes_password_by_default(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('firstName', $array);
        $this->assertArrayHasKey('lastName', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'TestPass123'
        ];

        $config = AdminConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'firstName', 'Test');
        $this->assertPropertyEquals($config, 'lastName', 'User');
        $this->assertPropertyEquals($config, 'email', 'test@example.com');
        $this->assertPropertyEquals($config, 'username', 'testuser');
        $this->assertPropertyEquals($config, 'password', 'TestPass123');
    }

    public function test_from_array_with_missing_fields_uses_empty_strings(): void
    {
        $data = ['firstName' => 'John'];

        $config = AdminConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'firstName', 'John');
        $this->assertPropertyEquals($config, 'lastName', '');
        $this->assertPropertyEquals($config, 'email', '');
        $this->assertPropertyEquals($config, 'username', '');
        $this->assertPropertyEquals($config, 'password', '');
    }
}
