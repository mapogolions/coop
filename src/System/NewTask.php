<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\System;

use Mapogolions\Suspendable\{ Task, Scheduler };

final class NewTask extends SystemCall
{
  private $suspendable;

  public function __construct(\Generator $suspendable)
  {
    $this->suspendable = $suspendable;
  }

  public function handle(Task $task, Scheduler $scheduler): void
  {
    $scheduler->spawn($this->suspendable);
    $task->setValue(count($scheduler->tasksPool()));
    $scheduler->schedule($task);
  }
}