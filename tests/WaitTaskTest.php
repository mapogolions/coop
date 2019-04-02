<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ NewTask, WaitTask };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class WaitTaskTest extends TestCase
{
  public function testParentTaskWaitsForTerminatedDerivedTask()
  {
    $spy = new Spy();
    $suspendable = (function () use ($spy) {
      yield "start";
      $childTid = yield new NewTask(
        TestKit::trackedAsDataProducer(TestKit::countup(3), $spy)
      );
      yield new WaitTask($childTid);
      yield "end";
    })();
    $pl = new Scheduler();
    $pl
      ->spawn(
        TestKit::trackedAsDataProducer($suspendable, $spy, TestKit::ignoreSystemCalls())
      )
      ->launch();
    $this->assertEquals(["start", 1, 2, 3, "end"], $spy->calls());
    $this->assertEquals([], $pl->defferedTasksPool());
  }
}