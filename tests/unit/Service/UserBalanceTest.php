<?php

namespace UnitTests\Service;

use App\Entity\Currency;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use App\Service\ExchangeService;
use App\Service\UserBalanceService;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class UserBalanceTest extends TestCase
{
    private static array $currencies = [
        'RUB' => 1,
        'USD' => 100,
        'EUR' => 120,
    ];

    private static UserBalanceService $userBalanceService;
    private static EntityManagerInterface|MockInterface $entityManager;

    public static function setUpBeforeClass(): void
    {
        /** @var MockInterface|EntityManagerInterface $repository */
        self::$entityManager = Mockery::mock(EntityManagerInterface::class);
        self::$entityManager->shouldReceive('persist');
        self::$entityManager->shouldReceive('flush');

        self::$userBalanceService = new UserBalanceService(
            self::$entityManager,
            new ExchangeService(),
        );
    }

    public function sufficientBalanceDataProvider(): array
    {
        $userRub100 = $this->makeUser([
            'balance' => 100,
            'iso'     => 'RUB',
        ]);

        $userEur100 = $this->makeUser([
            'balance' => 100,
            'iso'     => 'EUR',
        ]);

        return [
            'positive_eur100_rub100' => [
                $userEur100,
                100,
                $this->makeCurrency('RUB'),
                true,
            ],
            'positive_rub100_rub100' => [
                $userRub100,
                100,
                $this->makeCurrency('RUB'),
                true,
            ],
            'negative_rub100_eur100' => [
                $userRub100,
                100,
                $this->makeCurrency('EUR'),
                false,
            ],
            'negative_eur100_eur200' => [
                $userEur100,
                200,
                $this->makeCurrency('EUR'),
                false,
            ],
        ];
    }

    /**
     * @dataProvider sufficientBalanceDataProvider
     */
    public function testBalanceSufficient(User $user, float $amount, Currency $currency, bool $expected): void
    {
        static::assertSame($expected, self::$userBalanceService->isBalanceSufficient($user, $amount, $currency));
    }

    public function updateBalanceDataProvider(): array
    {
        return [
            'up_100rub' => [
                $this->makeTransaction(
                    [
                        'amount'    => 200,
                        'iso'       => 'RUB',
                        'direction' => Direction::DEPOSIT,
                        'status'    => Status::SUCCESS,
                    ],
                    $this->makeUser([
                        'balance' => 100,
                        'iso'     => 'RUB',
                    ]),
                ),
                300.00
            ],
            'up_10eur' => [
                $this->makeTransaction(
                    [
                        'amount'    => 10,
                        'iso'       => 'EUR',
                        'direction' => Direction::DEPOSIT,
                        'status'    => Status::SUCCESS,
                    ],
                    $this->makeUser([
                        'balance' => 100,
                        'iso'     => 'RUB',
                    ]),
                ),
                1300.00,
            ],
            'up_10eur_pending' => [
                $this->makeTransaction(
                    [
                        'amount'    => 10,
                        'iso'       => 'EUR',
                        'direction' => Direction::DEPOSIT,
                        'status'    => Status::PENDING,
                    ],
                    $this->makeUser([
                        'balance' => 100,
                        'iso'     => 'RUB',
                    ]),
                ),
                100.00,
            ],
            'down_100rub' => [
                $this->makeTransaction(
                    [
                        'amount'    => 100,
                        'iso'       => 'RUB',
                        'direction' => Direction::WITHDRAW,
                        'status'    => Status::NEW,
                    ],
                    $this->makeUser([
                        'balance' => 500,
                        'iso'     => 'RUB',
                    ]),
                ),
                400.00,
            ],
            'down_1000rub' => [
                $this->makeTransaction(
                    [
                        'amount'    => 1000,
                        'iso'       => 'RUB',
                        'direction' => Direction::WITHDRAW,
                        'status'    => Status::NEW,
                    ],
                    $this->makeUser([
                        'balance' => 500,
                        'iso'     => 'RUB',
                    ]),
                ),
                500,
            ],
            'down_100eur' => [
                $this->makeTransaction(
                    [
                        'amount'    => 100,
                        'iso'       => 'EUR',
                        'direction' => Direction::WITHDRAW,
                        'status'    => Status::NEW,
                    ],
                    $this->makeUser([
                        'balance' => 500,
                        'iso'     => 'RUB',
                    ]),
                ),
                500,
            ],
        ];
    }

    /**
     * @dataProvider updateBalanceDataProvider
     */
    public function testUpBalance(Transaction $transaction, float $expected): void
    {
        self::$userBalanceService->updateBalance($transaction);

        static::assertSame($expected, $transaction->getPayer()->getBalance());
    }

    private function makeUser(array $data): User
    {
        $user = new User();
        $user->setBalance($data['balance']);
        $user->setCurrency($this->makeCurrency($data['iso']));

        return $user;
    }

    private function makeCurrency(string $iso): Currency
    {
        $currency = new Currency();
        $currency->setIso($iso);
        $currency->setRate(static::$currencies[$iso] ?? 1);

        return $currency;
    }

    private function makeTransaction(array $data, User $user): Transaction
    {
        $transaction = new Transaction();
        $transaction->setAmount($data['amount']);
        $transaction->setCurrency($this->makeCurrency($data['iso']));
        $transaction->setDirection($data['direction']);
        $transaction->setStatus($data['status']);
        $transaction->setPayer($user);

        return $transaction;
    }
}