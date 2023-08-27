<?php

namespace UnitTests\Service;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\Method;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use App\Manager\MethodManager;
use App\Manager\TransactionManager;
use App\Manager\UserManager;
use App\Service\AsyncService;
use App\Service\ExchangeService;
use App\Service\TransactionService;
use App\Service\UserBalanceService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TransactionServiceTest extends TestCase
{
    /** @var EntityManagerInterface|MockInterface */
    private static $entityManager;

    private static TransactionService $transactionService;

    public static function setUpBeforeClass(): void
    {
        /** @var MockInterface|EntityRepository $repository */
        $repository = Mockery::mock(EntityRepository::class);

        /** @var MockInterface|EntityManagerInterface $repository */
        self::$entityManager = Mockery::mock(EntityManagerInterface::class);
        self::$entityManager->shouldReceive('persist');
        self::$entityManager->shouldReceive('flush');

        $cache  = Mockery::mock(TagAwareCacheInterface::class);
        $hasher = Mockery::mock(UserPasswordHasherInterface::class);
        $auth   = Mockery::mock(AuthorizationCheckerInterface::class);

        $methodManager      = new MethodManager(self::$entityManager, $cache);
        $userManager        = new UserManager(self::$entityManager, $hasher, $cache);
        $transactionManager = new TransactionManager(self::$entityManager);
        $userBalanceService = new UserBalanceService(self::$entityManager, new ExchangeService());

        self::$transactionService = new TransactionService(
            $methodManager,
            $userManager,
            $transactionManager,
            $userBalanceService,
            new ExchangeService(),
            new AsyncService(),
            $auth,
        );
    }

    private function isAllowedToChangeStatusDataProvider(): array
    {
        return [
            'deposit_new_fail'        => [
                $this->makeTransaction(Status::NEW, Direction::DEPOSIT),
                Status::FAIL,
                true,
            ],
            'deposit_fail_success'    => [
                $this->makeTransaction(Status::FAIL, Direction::DEPOSIT),
                Status::SUCCESS,
                true,
            ],
            'deposit_success_pending' => [
                $this->makeTransaction(Status::SUCCESS, Direction::DEPOSIT),
                Status::PENDING,
                false,
                'Transaction already has a final status',
            ],
            'deposit_pending_pending' => [
                $this->makeTransaction(Status::PENDING, Direction::DEPOSIT),
                Status::PENDING,
                false,
                'Status is same',
            ],
            'withdraw_new_fail'       => [
                $this->makeTransaction(Status::NEW, Direction::WITHDRAW),
                Status::FAIL,
                true,
            ],
            'withdraw_fail_success'   => [
                $this->makeTransaction(Status::FAIL, Direction::WITHDRAW),
                Status::SUCCESS,
                false,
                'Transaction already has a final status',
            ],
        ];
    }

    /**
     * @dataProvider isAllowedToChangeStatusDataProvider
     */
    public function testIsAllowedToChangeStatus(Transaction $transaction, Status $status, bool $expected, ?string $exceptionMessage = ''): void
    {
        if ($exceptionMessage !== '') {
            static::expectExceptionMessage($exceptionMessage);
        }

        static::assertSame($expected, self::$transactionService->isAllowedToChangeStatus($transaction, $status));
    }

    private function isAllowedTransactionCreateDataProvider(): array
    {
        $russia  = $this->makeCountry('Russia');
        $germany = $this->makeCountry('Germany');

        $ivan = $this->makeUser('Ivan', $russia);

        $beeline = $this->makeMethod('Beeline', [$russia]);
        $visa    = $this->makeMethod('Visa', [$russia, $germany]);
        $sofort  = $this->makeMethod('Sofort', [$germany]);

        return [
            'russia_beeline' => [
                $this->makeTransaction(Status::NEW, Direction::DEPOSIT, $ivan, $beeline),
                true,
            ],
            'russia_visa' => [
                $this->makeTransaction(Status::NEW, Direction::DEPOSIT, $ivan, $visa),
                true,
            ],
            'russia_sofort' => [
                $this->makeTransaction(Status::NEW, Direction::DEPOSIT, $ivan, $sofort),
                false,
                "Method Sofort is not allowed for user"
            ],
            'not_new' => [
                $this->makeTransaction(Status::PENDING, Direction::DEPOSIT, $ivan, $beeline),
                false,
                'Incorrect status'
            ],
        ];
    }

    /**
     * @dataProvider isAllowedTransactionCreateDataProvider
     */
    public function testIsAllowedTransactionCreate(Transaction $transaction, bool $expected, ?string $exceptionMessage = ''): void
    {
        if ($exceptionMessage !== '') {
            static::expectExceptionMessage($exceptionMessage);
        }

        static::assertSame($expected, self::$transactionService->isAllowedTransactionCreate($transaction));
    }

    private function makeTransaction(Status $status, Direction $direction, User $user = new User(), Method $method = new Method()): Transaction
    {
        return (new Transaction())
            ->setDirection($direction)
            ->setStatus($status)
            ->setPayer($user)
            ->setMethod($method)
            ->setAmount(100)
            ->setCurrency((new Currency())->setRate(1));
    }

    private function makeMethod(string $name, array $countries): Method
    {
        $method = (new Method())
            ->setName($name);

        foreach ($countries as $country) {
            $method->addCountry($country);
        }

        return $method;
    }

    private function makeUser(string $login, Country $country): User
    {
        return (new User)
            ->setLogin($login)
            ->setCountry($country)
            ->setBalance(1000);
    }

    private function makeCountry(string $name): Country
    {
        return (new Country())
            ->setName($name);
    }
}