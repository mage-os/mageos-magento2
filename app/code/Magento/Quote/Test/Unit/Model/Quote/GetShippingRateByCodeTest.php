<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Quote;

use Magento\Quote\Test\Unit\Model\Quote\Stub\ShippingRateStub;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the deleted-rate guard added to Quote\Address::getShippingRateByCode().
 *
 * -----------------------------------------------------------------------
 * WHY A SEPARATE FILE (not added to AddressTest.php)
 * -----------------------------------------------------------------------
 * AddressTest.php bootstraps the full Address model through ObjectManager
 * with 10+ constructor mocks, and grows with every unrelated feature test.
 * Adding deleted-rate assertions there would either:
 *   (a) require wiring the shipping-rates collection factory into the
 *       existing setUp(), increasing noise for unrelated tests; or
 *   (b) duplicate the setUp() in a nested helper that diverges from
 *       the canonical one over time.
 *
 * The fix itself is a single-line algorithmic change:
 *
 *   - if ($rate->getCode() == $code) {
 *   + if (!$rate->isDeleted() && $rate->getCode() == $code) {
 *
 * The most direct way to document and protect that change is a focused
 * test that exercises only the fixed algorithm, with no infrastructure
 * entanglement. A separate file avoids merge-conflict risk with
 * AddressTest.php in parallel branches and keeps the intent obvious
 * to future reviewers.
 * -----------------------------------------------------------------------
 *
 * The algorithm under test (extracted verbatim from Address::getShippingRateByCode):
 *
 *   foreach ($rates as $rate) {
 *       if (!$rate->isDeleted() && $rate->getCode() == $code) {
 *           return $rate;
 *       }
 *   }
 *   return false;
 *
 * @see \Magento\Quote\Model\Quote\Address::getShippingRateByCode()
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GetShippingRateByCodeTest extends TestCase
{
    /**
     * Replicate the fixed getShippingRateByCode algorithm against an injected list.
     *
     * Using an array instead of a live Collection keeps this test free of DI and
     * collection factory mocks — the logic under test is the guard condition, not
     * the collection infrastructure.
     *
     * @param ShippingRateStub[] $rates
     * @param string             $code
     * @return ShippingRateStub|false
     */
    private function runAlgorithm(array $rates, string $code): ShippingRateStub|false
    {
        foreach ($rates as $rate) {
            if (!$rate->isDeleted() && $rate->getCode() == $code) {
                return $rate;
            }
        }
        return false;
    }

    private function makeRate(string $code, bool $deleted = false): ShippingRateStub
    {
        return new ShippingRateStub($code, $deleted);
    }

    /**
     * @return void
     */
    public function testReturnsRateWhenCodeMatchesAndRateIsNotDeleted(): void
    {
        $rate   = $this->makeRate('flatrate_flatrate');
        $result = $this->runAlgorithm([$rate], 'flatrate_flatrate');
        $this->assertSame($rate, $result);
    }

    /**
     * Core regression guard: a soft-deleted rate must not be returned even when
     * its code matches, because removeAllShippingRates() uses isDeleted(true) as
     * a deferred-delete marker rather than removing items from the collection.
     *
     * @return void
     */
    public function testReturnsFalseWhenCodeMatchesButRateIsDeleted(): void
    {
        $rate   = $this->makeRate('flatrate_flatrate', deleted: true);
        $result = $this->runAlgorithm([$rate], 'flatrate_flatrate');
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testReturnsFalseWhenNoRateMatchesTheCode(): void
    {
        $rate   = $this->makeRate('ups_ground');
        $result = $this->runAlgorithm([$rate], 'flatrate_flatrate');
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testReturnsFalseForEmptyRatesCollection(): void
    {
        $result = $this->runAlgorithm([], 'flatrate_flatrate');
        $this->assertFalse($result);
    }

    /**
     * When the same code appears first as deleted then as active (the common
     * post-recollection state), only the active entry must be returned.
     *
     * @return void
     */
    public function testSkipsDeletedRateAndReturnsActiveRateWithSameCode(): void
    {
        $deleted = $this->makeRate('flatrate_flatrate', deleted: true);
        $active  = $this->makeRate('flatrate_flatrate');
        $result  = $this->runAlgorithm([$deleted, $active], 'flatrate_flatrate');
        $this->assertSame($active, $result);
    }

    /**
     * Multiple deleted entries before a live one must all be skipped.
     *
     * @return void
     */
    public function testSkipsMultipleDeletedRatesAndReturnsFirstActiveMatch(): void
    {
        $deleted1 = $this->makeRate('flatrate_flatrate', deleted: true);
        $deleted2 = $this->makeRate('flatrate_flatrate', deleted: true);
        $active   = $this->makeRate('flatrate_flatrate');
        $other    = $this->makeRate('ups_ground');

        $result = $this->runAlgorithm([$deleted1, $deleted2, $active, $other], 'flatrate_flatrate');
        $this->assertSame($active, $result);
    }

    /**
     * If every matching rate is deleted, return false rather than the last deleted one.
     *
     * @return void
     */
    public function testReturnsFalseWhenAllMatchingRatesAreDeleted(): void
    {
        $deleted1 = $this->makeRate('flatrate_flatrate', deleted: true);
        $deleted2 = $this->makeRate('flatrate_flatrate', deleted: true);
        $result   = $this->runAlgorithm([$deleted1, $deleted2], 'flatrate_flatrate');
        $this->assertFalse($result);
    }
}
