<?php

declare(strict_types=1);

namespace Arty\ProbeBundle\Tests\Probe;

use Arty\ProbeBundle\Entity\ProbeStatusHistory;
use Arty\ProbeBundle\Model\AlertManagerInterface;
use Arty\ProbeBundle\Model\ProbeManagerInterface;
use Arty\ProbeBundle\Model\ProbeStatus;
use Arty\ProbeBundle\ProbeRunner;
use Arty\ProbeBundle\Tests\Fixtures\FailureProbe;
use Arty\ProbeBundle\Tests\Fixtures\SuccessProbe;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class TestProbeStatusHistory extends ProbeStatusHistory
{
}

class ProbeRunnerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ProbeManagerInterface> */
    private ObjectProphecy $probeManager;

    /** @var ObjectProphecy<AlertManagerInterface> */
    private ObjectProphecy $alertManager;

    protected function setUp(): void
    {
        $this->probeManager = $this->prophesize(ProbeManagerInterface::class);
        $this->alertManager = $this->prophesize(AlertManagerInterface::class);
    }

    public function testRunAll(): void
    {
        $successProbe = new SuccessProbe();
        $failureProbe = new FailureProbe();

        $probesByName = [
            'success_probe' => [
                'probeInstance' => $successProbe,
                'name' => 'success_probe',
                'successThreshold' => 0,
                'warningThreshold' => 1,
                'failureThreshold' => 2,
                'description' => 'Success description',
                'notify' => true,
            ],
            'failure_probe' => [
                'probeInstance' => $failureProbe,
                'name' => 'failure_probe',
                'successThreshold' => 0,
                'warningThreshold' => 1,
                'failureThreshold' => 2,
                'description' => 'Failure description',
                'notify' => true,
            ],
        ];

        $runner = new ProbeRunner($probesByName, $this->probeManager->reveal(), $this->alertManager->reveal());

        $this->probeManager->create(
            'success_probe',
            'Success description',
            ProbeStatus::SUCCESS,
            Argument::type(\DateTimeImmutable::class)
        )->willReturn(new TestProbeStatusHistory('success_probe', 'Success description', ProbeStatus::SUCCESS, new \DateTimeImmutable()))
        ->shouldBeCalled();

        $this->probeManager->create(
            'failure_probe',
            'Failure description',
            ProbeStatus::FAILED,
            Argument::type(\DateTimeImmutable::class)
        )->willReturn(new TestProbeStatusHistory('failure_probe', 'Failure description', ProbeStatus::FAILED, new \DateTimeImmutable()))
        ->shouldBeCalled();

        $this->probeManager->save(Argument::type(ProbeStatusHistory::class))
            ->shouldBeCalledTimes(2);

        $this->probeManager->findLastByProbeName('failure_probe')->willReturn(null)->shouldBeCalled();

        $this->alertManager->sendAlert(Argument::type(ProbeStatusHistory::class))->shouldBeCalledOnce();

        $results = $runner->runAll();

        $this->assertCount(2, $results);
        $this->assertEquals('success_probe', $results[0]->probeName);
        $this->assertEquals(ProbeStatus::SUCCESS, $results[0]->status);
        $this->assertEquals('failure_probe', $results[1]->probeName);
        $this->assertEquals(ProbeStatus::FAILED, $results[1]->status);
    }

    public function testRunSendsAlertOnFailure(): void
    {
        $failureProbe = new FailureProbe();
        $probesByName = [
            'failure_probe' => [
                'probeInstance' => $failureProbe,
                'name' => 'failure_probe',
                'successThreshold' => 0,
                'warningThreshold' => 1,
                'failureThreshold' => 2,
                'description' => 'Failure description',
                'notify' => true,
            ],
        ];

        $runner = new ProbeRunner($probesByName, $this->probeManager->reveal(), $this->alertManager->reveal());

        $probeStatusHistory = new TestProbeStatusHistory('failure_probe', 'Failure description', ProbeStatus::FAILED, new \DateTimeImmutable());

        $this->probeManager->create(
            'failure_probe',
            'Failure description',
            ProbeStatus::FAILED,
            Argument::type(\DateTimeImmutable::class)
        )->willReturn($probeStatusHistory)
        ->shouldBeCalled();

        $this->probeManager->findLastByProbeName('failure_probe')->willReturn(null)->shouldBeCalled();
        $this->probeManager->save($probeStatusHistory)->shouldBeCalled();

        $this->alertManager->sendAlert($probeStatusHistory)->shouldBeCalled();

        $runner->run('failure_probe');
    }

    public function testRunDoesNotSendAlertIfAlreadyFailed(): void
    {
        $failureProbe = new FailureProbe();
        $probesByName = [
            'failure_probe' => [
                'probeInstance' => $failureProbe,
                'name' => 'failure_probe',
                'successThreshold' => 0,
                'warningThreshold' => 1,
                'failureThreshold' => 2,
                'description' => 'Failure description',
                'notify' => true,
            ],
        ];

        $runner = new ProbeRunner($probesByName, $this->probeManager->reveal(), $this->alertManager->reveal());

        $probeStatusHistory = new TestProbeStatusHistory('failure_probe', 'Failure description', ProbeStatus::FAILED, new \DateTimeImmutable());
        $lastStatusHistory = new TestProbeStatusHistory('failure_probe', 'Failure description', ProbeStatus::FAILED, new \DateTimeImmutable());

        $this->probeManager->create(
            'failure_probe',
            'Failure description',
            ProbeStatus::FAILED,
            Argument::type(\DateTimeImmutable::class)
        )->willReturn($probeStatusHistory)
        ->shouldBeCalled();

        $this->probeManager->findLastByProbeName('failure_probe')->willReturn($lastStatusHistory)->shouldBeCalled();
        $this->probeManager->save($probeStatusHistory)->shouldBeCalled();

        $this->alertManager->sendAlert(Argument::any())->shouldNotBeCalled();

        $runner->run('failure_probe');
    }

    public function testRunWithoutAlerting(): void
    {
        $failureProbe = new FailureProbe();
        $probesByName = [
            'failure_probe' => [
                'probeInstance' => $failureProbe,
                'name' => 'failure_probe',
                'successThreshold' => 0,
                'warningThreshold' => 1,
                'failureThreshold' => 2,
                'description' => 'Failure description',
                'notify' => true,
            ],
        ];

        $runner = new ProbeRunner($probesByName, $this->probeManager->reveal(), null);

        $probeStatusHistory = new TestProbeStatusHistory('failure_probe', 'Failure description', ProbeStatus::FAILED, new \DateTimeImmutable());

        $this->probeManager->create(
            'failure_probe',
            'Failure description',
            ProbeStatus::FAILED,
            Argument::type(\DateTimeImmutable::class)
        )->willReturn($probeStatusHistory)
        ->shouldBeCalled();

        $this->probeManager->save($probeStatusHistory)->shouldBeCalled();

        $runner->run('failure_probe');
    }

    public function testRunDoesNotSendAlertWhenNotifyIsFalse(): void
    {
        $failureProbe = new FailureProbe();
        $probesByName = [
            'failure_probe' => [
                'probeInstance' => $failureProbe,
                'name' => 'failure_probe',
                'successThreshold' => 0,
                'warningThreshold' => 1,
                'failureThreshold' => 2,
                'description' => 'Failure description',
                'notify' => false,
            ],
        ];

        $runner = new ProbeRunner($probesByName, $this->probeManager->reveal(), $this->alertManager->reveal());

        $probeStatusHistory = new TestProbeStatusHistory('failure_probe', 'Failure description', ProbeStatus::FAILED, new \DateTimeImmutable());

        $this->probeManager->create(
            'failure_probe',
            'Failure description',
            ProbeStatus::FAILED,
            Argument::type(\DateTimeImmutable::class)
        )->willReturn($probeStatusHistory)
        ->shouldBeCalled();

        $this->probeManager->findLastByProbeName('failure_probe')->willReturn(null)->shouldBeCalled();
        $this->probeManager->save($probeStatusHistory)->shouldBeCalled();

        $this->alertManager->sendAlert(Argument::any())->shouldNotBeCalled();

        $runner->run('failure_probe');
    }
}
