<?php

namespace UnitTests\Service;

use App\Entity\Currency;
use App\Entity\User;
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
        /** @var UserPasswordHasherInterface $encoder */
        $encoder = self::getContainer()->get('security.password_hasher');
        /** @var TagAwareCacheInterface $cache */
        $cache = self::getContainer()->get('redis_adapter');

        $userManager = new UserManager($this->getDoctrineManager(), $encoder, $cache);
        $currencyManager = new CurrencyManager($this->getDoctrineManager());

        $userEur100 = $userManager->findByLogin(MultipleUsersFixture::EUR_100);
        $rub = $currencyManager->findByIso(MultipleCurrenciesFixture::RUB);

        return [
            'eur100_rub100' => [
                $userEur100,
                100,
                $rub,
                true,
            ],
        ];
    }

    /**
     * @dataProvider sufficientBalanceDataProvider
     */
    public function testBalanceSufficient(User $user, float $amount, Currency $currency, bool $expected): void
    {
        $userBalanceService = new UserBalanceService(
            $this->getDoctrineManager(),
            new ExchangeService(),
        );

        static::assertSame($expected, $userBalanceService->isBalanceSufficient($user, $amount, $currency));
    }
}