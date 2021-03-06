<?php declare(strict_types=1);
/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (C) 2017-2019 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Tests;

use CleverAge\ProcessBundle\Context\ContextualOptionResolver;
use CleverAge\ProcessBundle\Event\ProcessEvent;
use CleverAge\ProcessBundle\Logger\ProcessLogger;
use CleverAge\ProcessBundle\Logger\TaskLogger;
use CleverAge\ProcessBundle\Manager\ProcessManager;
use CleverAge\ProcessBundle\Registry\ProcessConfigurationRegistry;
use Prophecy\Argument\Token\TypeToken;
use Prophecy\Prophecy\MethodProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProcessManagerTest extends AbstractProcessTest
{

    public function testProcessEvents()
    {
        $edProphecy = $this->prophesize(EventDispatcherInterface::class);

        $dispatchStartProphecy = new MethodProphecy($edProphecy, 'dispatch', [ProcessEvent::EVENT_PROCESS_STARTED, new TypeToken(ProcessEvent::class)]);
        $dispatchStartProphecy->shouldBeCalled();
        $edProphecy->addMethodProphecy($dispatchStartProphecy);

        $dispatchStartProphecy = new MethodProphecy($edProphecy, 'dispatch', [ProcessEvent::EVENT_PROCESS_ENDED, new TypeToken(ProcessEvent::class)]);
        $dispatchStartProphecy->shouldBeCalled();
        $edProphecy->addMethodProphecy($dispatchStartProphecy);

        $dispatchStartProphecy = new MethodProphecy($edProphecy, 'dispatch', [ProcessEvent::EVENT_PROCESS_FAILED, new TypeToken(ProcessEvent::class)]);
        $dispatchStartProphecy->shouldNotBeCalled();
        $edProphecy->addMethodProphecy($dispatchStartProphecy);

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $edProphecy->reveal();
        $processManager = new ProcessManager(
            self::$container,
            self::$container->get(ProcessLogger::class),
            self::$container->get(TaskLogger::class),
            self::$container->get(ProcessConfigurationRegistry::class),
            self::$container->get(ContextualOptionResolver::class),
            $eventDispatcher
        );

        $processManager->execute('test.simple_process');
    }
}
