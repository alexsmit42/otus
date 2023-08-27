<?php

namespace UnitTests\Service;

use App\Manager\CurrencyManager;
use App\Manager\UserManager;
use App\Service\ExchangeService;
use App\Service\UserBalanceService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use UnitTests\FixturedTestCase;
use UnitTests\Fixtures\MultipleCountriesFixture;
use UnitTests\Fixtures\MultipleCurrenciesFixture;
use UnitTests\Fixtures\MultipleUsersFixture;

class UserBalanceFixturesTest extends FixturedTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->addFixture(new MultipleCountriesFixture());
        $this->addFixture(new MultipleCurrenciesFixture());
        $this->addFixture(new MultipleUsersFixture());
        $this->executeFixtures();
    }

    public function sufficientBalanceDataProvider(): array
    {
        return [
            'eur100_rub100' => [
                MultipleUsersFixture::GE_EUR_100,
                100,
                MultipleCurrenciesFixture::RUB,
                true,
            ],
            'rub100_eur100' => [
                MultipleUsersFixture::RU_RUB_100,
                100,
                MultipleCurrenciesFixture::EUR,
                false,
            ],
            'rub100_rub100' => [
                MultipleUsersFixture::RU_RUB_100,
                100,
                MultipleCurrenciesFixture::RUB,
                true,
            ],
            'usd100_rub10000' => [
                MultipleUsersFixture::RU_USD_100,
                10000,
                MultipleCurrenciesFixture::RUB,
                true,
            ],
            'rub0_rub100' => [
                MultipleUsersFixture::RU_RUB_0,
                100,
                MultipleCurrenciesFixture::RUB,
                false,
            ],
        ];
    }

    /**
     * @dataProvider sufficientBalanceDataProvider
     */
    public function testBalanceSufficient(string $login, float $amount, string $iso, bool $expected): void
    {
        /** @var UserPasswordHasherInterface $encoder */
        $encoder = self::getContainer()->get('security.password_hasher');
        /** @var TagAwareCacheInterface $cache */
        $cache = self::getContainer()->get('redis_adapter');

        $userManager = new UserManager($this->getDoctrineManager(), $encoder, $cache);
        $currencyManager = new CurrencyManager($this->getDoctrineManager());
        $userBalanceService = new UserBalanceService(
            $this->getDoctrineManager(),
            new ExchangeService(),
        );

        $user = $userManager->findByLogin($login);
        $currency = $currencyManager->findByIso($iso);

        static::assertSame($expected, $userBalanceService->isBalanceSufficient($user, $amount, $currency));
    }
}