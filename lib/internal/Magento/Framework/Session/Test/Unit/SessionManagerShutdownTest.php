<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

// @codingStandardsIgnoreStart
namespace Magento\Framework\Session {
    use Magento\Framework\Session\Test\Unit\SessionManagerShutdownTest;

    if (!function_exists(__NAMESPACE__ . '\register_shutdown_function')) {
        /**
         * Capture the shutdown functions SessionManager registers while a test asks for it
         *
         * @param callable $callback
         * @param mixed ...$args
         * @return void
         */
        function register_shutdown_function(callable $callback, mixed ...$args): void
        {
            if (SessionManagerShutdownTest::$capture) {
                SessionManagerShutdownTest::$callbacks[] = $callback;
                return;
            }
            \register_shutdown_function($callback, ...$args);
        }
    }
}

namespace Magento\Framework\Session\Test\Unit {
    // @codingStandardsIgnoreEnd
    use Magento\Framework\Session\SessionManager;
    use PHPUnit\Framework\TestCase;

    /**
     * The shutdown function of a session manager must close the session without keeping the manager alive
     */
    class SessionManagerShutdownTest extends TestCase
    {
        /**
         * @var bool
         */
        public static bool $capture = false;

        /**
         * @var callable[]
         */
        public static array $callbacks = [];

        /**
         * @inheritdoc
         */
        protected function setUp(): void
        {
            self::$capture = true;
            self::$callbacks = [];
        }

        /**
         * @inheritdoc
         */
        protected function tearDown(): void
        {
            self::$capture = false;
            self::$callbacks = [];
        }

        /**
         * A session manager that is still in use closes the session when the script ends
         *
         * @return void
         */
        public function testShutdownFunctionClosesTheSession(): void
        {
            $sessionManager = $this->createSessionManager();
            $sessionManager->registerShutdown();

            $this->assertCount(1, self::$callbacks);
            (self::$callbacks[0])();
            $this->assertSame(1, $sessionManager->closeCount);
        }

        /**
         * A discarded session manager is freed although its shutdown function is still registered
         *
         * @return void
         */
        public function testShutdownFunctionDoesNotKeepTheSessionManagerAlive(): void
        {
            $sessionManager = $this->createSessionManager();
            $sessionManager->registerShutdown();
            $reference = \WeakReference::create($sessionManager);
            unset($sessionManager);

            $this->assertNull($reference->get(), 'The shutdown function keeps the session manager alive.');
            (self::$callbacks[0])();
        }

        /**
         * Create a session manager that counts how often it closes the session
         *
         * Not a PHPUnit mock: the test case keeps references to its mocks, so they could never be freed.
         *
         * @return SessionManager
         */
        private function createSessionManager(): SessionManager
        {
            return new class extends SessionManager {
                /**
                 * @var int
                 */
                public int $closeCount = 0;

                /**
                 * No dependencies are needed to register and run the shutdown function
                 */
                public function __construct()
                {
                    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
                }

                /**
                 * @inheritdoc
                 */
                public function writeClose()
                {
                    $this->closeCount++;
                }
            };
        }
    }
}
