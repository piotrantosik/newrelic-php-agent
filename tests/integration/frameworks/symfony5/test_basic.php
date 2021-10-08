<?php
/*
 * Copyright 2020 New Relic Corporation. All rights reserved.
 * SPDX-License-Identifier: Apache-2.0
 */

/*DESCRIPTION
The agent should name Symfony 5 transactions that have _route attributes.
*/

/*EXPECT_METRICS
[
  "?? agent run id",
  "?? timeframe start",
  "?? timeframe stop",
  [
    [{"name":"OtherTransaction/all"},                     [1, "??", "??", "??", "??", "??"]],
    [{"name":"OtherTransactionTotalTime"},                [1, "??", "??", "??", "??", "??"]],
    [{"name":"OtherTransactionTotalTime/Action/GET_foo"}, [1, "??", "??", "??", "??", "??"]],
    [{"name":"OtherTransaction/Action/GET_foo"},          [1, "??", "??", "??", "??", "??"]],
    [{"name":"Supportability/framework/Silex/detected"},  [1,    0,    0,    0,    0,    0]]
  ]
]
*/

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;

include __DIR__ . '/Framework/Application.php';

$routerListener = new RouterListener();

$eventDispatcher = new EventDispatcher();
$eventDispatcher->addSubscriber($routerListener);

$kernel = new HttpKernel($eventDispatcher);
$request = new Request;
$request->attributes = new ParameterBag;
$request->attributes->_route = 'GET_foo';

$kernel->handle($request);
