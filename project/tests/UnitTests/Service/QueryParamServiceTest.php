<?php

namespace App\Tests\UnitTests\Service;

use ReflectionProperty;
use PHPUnit\Framework\TestCase;
use App\Service\QueryParam\QueryParamService;
use Symfony\Component\HttpFoundation\Request;
use App\Service\QueryParam\Order\OrderService;
use App\Service\QueryParam\Filter\FilterService;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryParamServiceTest extends TestCase
{
    public function testGetFiltersWithValidFilterService(): void
    {
        // Мок FilterService
        $filterServiceMock = $this->getMockBuilder(FilterService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFilters'])
            ->getMock();

        $filterServiceMock->expects($this->once())
            ->method('getFilters')
            ->willReturn((function() {
                yield 'filter1';
                yield 'filter2';
            })()); // Используем генератор

        // Мок Request с фильтрами
        $request = new Request();
        $request->query->set('f', ['company' => ['id' => ['eq' => 10]]]);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        // Создаем объект, передаем мок RequestStack
        $queryParamService = new QueryParamService($requestStack);

        // Устанавливаем приватное свойство filterService через Reflection
        $filterProperty = new ReflectionProperty(QueryParamService::class, 'filterService');
        $filterProperty->setValue($queryParamService, $filterServiceMock);

        $filters = iterator_to_array($queryParamService->getFilters());
        $this->assertEquals(['filter1', 'filter2'], $filters);
    }

    public function testGetOrdersWithValidOrderService(): void
    {
        // Мок OrderService
        $orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOrders'])
            ->getMock();

        $orderServiceMock->expects($this->once())
            ->method('getOrders')
            ->willReturn((function() {
                yield 'order1';
                yield 'order2';
            })()); // Используем генератор

        // Мок Request с сортировками
        $request = new Request();
        $request->query->set('s', ['company' => ['name' => 'desc']]);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $queryParamService = new QueryParamService($requestStack);

        // Устанавливаем приватное свойство orderService через Reflection
        $orderProperty = new ReflectionProperty(QueryParamService::class, 'orderService');
        $orderProperty->setValue($queryParamService, $orderServiceMock);

        $orders = iterator_to_array($queryParamService->getOrders());
        $this->assertEquals(['order1', 'order2'], $orders);
    }

    public function testDecodeRequestInitializesServices(): void
    {
        // Создаем Request с параметрами f(filter) и o(order)
        $request = new Request();
        $request->query->set('f', ['company' => ['id' => ['eq' => 10]]]);
        $request->query->set('o', ['company' => ['name' => 'desc']]);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $queryParamService = new QueryParamService($requestStack);

        // Проверяем что filterService проинициализирован (не null)
        $filterProperty = new ReflectionProperty(QueryParamService::class, 'filterService');
        $filterServiceValue = $filterProperty->getValue($queryParamService);

        $this->assertNotNull($filterServiceValue);
        $this->assertInstanceOf(FilterService::class, $filterServiceValue);

        // Проверяем что orderService проинициализирован (не null)
        $orderProperty = new ReflectionProperty(QueryParamService::class, 'orderService');
        $orderServiceValue = $orderProperty->getValue($queryParamService);

        $this->assertNotNull($orderServiceValue);
        $this->assertInstanceOf(OrderService::class, $orderServiceValue);
    }

    public function testGetFiltersReturnsEmptyWhenNoFilterService(): void
    {
        $requestStack = new RequestStack();
        // Нет параметров запроса, сервисы не проинициализированы
        $requestStack->push(new Request());

        $queryParamService = new QueryParamService($requestStack);

        $filters = iterator_to_array($queryParamService->getFilters());
        $this->assertEmpty($filters);
    }

    public function testGetOrdersReturnsEmptyWhenNoOrderService(): void
    {
        $requestStack = new RequestStack();
        // Нет параметров запроса, сервисы не проинициализированы
        $requestStack->push(new Request());

        $queryParamService = new QueryParamService($requestStack);

        $orders = iterator_to_array($queryParamService->getOrders());
        $this->assertEmpty($orders);
    }
}
