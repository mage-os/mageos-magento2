<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Model\Cart;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\LayoutFactory;

/**
 * Prepares storefront messages for AJAX add to cart responses.
 */
class AjaxMessageResponse
{
    /**
     * @param ManagerInterface $messageManager
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        private readonly ManagerInterface $messageManager,
        private readonly LayoutFactory $layoutFactory
    ) {
    }

    /**
     * Whether messages should be rendered on the current page instead of relying on redirect.
     *
     * @param string|null $backUrl
     * @param string|null $refererUrl
     * @return bool
     */
    public function shouldDisplayInline(?string $backUrl, ?string $refererUrl): bool
    {
        if ($backUrl === null || $backUrl === '') {
            return true;
        }

        if ($refererUrl === null || $refererUrl === '') {
            return false;
        }

        return $this->normalizeUrl($backUrl) === $this->normalizeUrl($refererUrl);
    }

    /**
     * Returns rendered messages for inline AJAX display.
     *
     * @param bool $clearMessages
     * @return array{html: string}|null
     */
    public function getInlineMessages(bool $clearMessages): ?array
    {
        $messages = $this->messageManager->getMessages($clearMessages);
        if (!$messages->getCount()) {
            return null;
        }

        $block = $this->layoutFactory->create()->getMessagesBlock();
        $block->setMessages($messages);

        return [
            'html' => $block->getGroupedHtml(),
        ];
    }

    /**
     * Normalizes a URL path for comparison by stripping query, fragment, and trailing slash.
     *
     * @param string $url
     * @return string
     */
    private function normalizeUrl(string $url): string
    {
        $normalizedUrl = explode('?', $url, 2)[0];
        $normalizedUrl = explode('#', $normalizedUrl, 2)[0];

        return rtrim($normalizedUrl, '/');
    }
}
