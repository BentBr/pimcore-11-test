<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\DefaultController;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Pimcore\Config;
use Pimcore\Templating\TwigDefaultDelegatingEngine;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/**
 * This basic unit test demonstrates how to unit-test a controller using mocked twig engine.
 */
class DefaultControllerTest extends Unit
{
    private DefaultController $controller;

    /**
     * @var Environment&MockObject $twig
     */
    private MockObject&Environment $twig;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a test double for twig engine.
        // See: https://phpunit.readthedocs.io/en/9.5/test-doubles.html
        $this->twig = $this->createMock(Environment::class);

        $container = new Container();
        $container->set('twig', $this->twig);
        $container->set('pimcore.templating', new TwigDefaultDelegatingEngine($this->twig, new Config()));

        $this->controller = new DefaultController();
        $this->controller->setContainer($container);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDefaultAction(): void
    {
        $this->twig->method('render')->willReturnMap(
            [
                // Simulate rendering of default template.
                ['default/default.html.twig', [], 'At pimcore we love writing tests! ❤️TDD!'],
            ]
        );

        $response = $this->controller->defaultAction($this->createMock(Request::class));
        $content = $response->getContent();
        self::assertIsString($content);

        self::assertEquals(200, $response->getStatusCode());
        self::assertStringContainsStringIgnoringCase('pimcore', $content);
        self::assertStringContainsStringIgnoringCase('❤', $content);
        self::assertStringContainsStringIgnoringCase('tests', $content);
        self::assertStringNotContainsStringIgnoringCase('bugs', $content);
        self::assertStringNotContainsStringIgnoringCase('hacks', $content);
        self::assertStringNotContainsStringIgnoringCase('💩', $content);
    }
}
