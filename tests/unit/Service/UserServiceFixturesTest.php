<?php

namespace UnitTests\Service;

use App\Entity\Method;
use App\Enum\Direction;
use App\Manager\UserManager;
use App\Service\UserService;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use UnitTests\FixturedTestCase;
use UnitTests\Fixtures\MultipleCountriesFixture;
use UnitTests\Fixtures\MultipleCurrenciesFixture;
use UnitTests\Fixtures\MultipleMethodsFixture;
use UnitTests\Fixtures\MultipleUsersFixture;

class UserServiceFixturesTest extends FixturedTestCase
{
    private UserManager $userManager;
    private UserService $userService;

    public function setUp(): void
    {
        parent::setUp();

        $this->addFixture(new MultipleCountriesFixture());
        $this->addFixture(new MultipleCurrenciesFixture());
        $this->addFixture(new MultipleUsersFixture());
        $this->addFixture(new MultipleMethodsFixture());
        $this->executeFixtures();

        /** @var UserPasswordHasherInterface $encoder */
        $encoder = self::getContainer()->get('security.password_hasher');
        /** @var TagAwareCacheInterface $cache */
        $cache = self::getContainer()->get('redis_adapter');

        $this->userManager = new UserManager($this->getDoctrineManager(), $encoder, $cache);
        $this->userService = new UserService($this->userManager, $cache);
    }

    public function availableMethodsDepositDataProvider(): array
    {
        return [
            'ru_user' => [
                MultipleUsersFixture::RU_RUB_0,
                [MultipleMethodsFixture::BEELINE, MultipleMethodsFixture::MIR, MultipleMethodsFixture::VISA],
            ],
            'ge_user' => [
                MultipleUsersFixture::GE_EUR_100,
                [MultipleMethodsFixture::VISA, MultipleMethodsFixture::SOFORT],
            ],
            'by_user' => [
                MultipleUsersFixture::BY_RUB_100,
                [],
            ],
        ];
    }

    /**
     * @dataProvider availableMethodsDepositDataProvider
     */
    public function testAvailableMethodsDeposit(string $login, array $expected): void
    {
        $user = $this->userManager->findByLogin($login);
        $methods = array_map(function (Method $method) {
            return $method->getName();
        }, $this->userService->getAvailableMethods($user));

        static::assertSame($expected, $methods);
    }

    public function availableMethodsWithdrawDataProvider(): array
    {
        return [
            'ru_rub_0'    => [
                MultipleUsersFixture::RU_RUB_0,
                [],
            ],
            'ru_rub_100'  => [
                MultipleUsersFixture::RU_RUB_100,
                [MultipleMethodsFixture::BEELINE, MultipleMethodsFixture::MIR],
            ],
            'ru_rub_1000' => [
                MultipleUsersFixture::RU_RUB_1000,
                [MultipleMethodsFixture::BEELINE, MultipleMethodsFixture::MIR, MultipleMethodsFixture::VISA],
            ],
            'ge_eur_100'  => [
                MultipleUsersFixture::GE_EUR_100,
                [MultipleMethodsFixture::VISA, MultipleMethodsFixture::SOFORT],
            ],
            'by_user'     => [
                MultipleUsersFixture::BY_RUB_100,
                [],
            ],
        ];
    }

    /**
     * @dataProvider availableMethodsWithdrawDataProvider
     */
    public function testAvailableMethodsWithdraw(string $login, array $expected): void
    {
        $user    = $this->userManager->findByLogin($login);
        $methods = array_map(function (Method $method) {
            return $method->getName();
        }, $this->userService->getAvailableMethods($user, Direction::WITHDRAW));

        static::assertSame($expected, $methods);
    }
}