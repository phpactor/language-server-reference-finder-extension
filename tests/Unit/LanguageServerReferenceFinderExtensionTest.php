<?php

namespace Phpactor\Extension\LanguageServerReferenceFinder\Tests\Unit;

use LanguageServerProtocol\TextDocumentIdentifier;
use LanguageServerProtocol\TextDocumentItem;
use PHPUnit\Framework\TestCase;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\LanguageServerReferenceFinder\LanguageServerReferenceFinderExtension;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\Extension\ReferenceFinder\ReferenceFinderExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\LanguageServer\Core\Rpc\ResponseError;
use Phpactor\LanguageServer\LanguageServerBuilder;
use Phpactor\LanguageServer\Test\ServerTester;

class LanguageServerReferenceFinderExtensionTest extends TestCase
{
    public function testDefinition()
    {
        $tester = $this->createTester();
        $tester->initialize();
        $tester->openDocument(new TextDocumentItem(__FILE__, 'php', 1, file_get_contents(__FILE__)));

        $responses = $tester->dispatch('textDocument/definition', [
            'textDocument' => new TextDocumentIdentifier(__FILE__),
            'position' => [
            ],
        ]);
        $response = $responses[0];
        $this->assertInstanceOf(ResponseError::class, $response->responseError);
        $this->assertContains('No definition locator', $response->responseError->message);
    }

    public function testTypeDefinition()
    {
        $tester = $this->createTester();
        $tester->initialize();
        $tester->openDocument(new TextDocumentItem(__FILE__, 'php', 1, file_get_contents(__FILE__)));

        $responses = $tester->dispatch('textDocument/typeDefinition', [
            'textDocument' => new TextDocumentIdentifier(__FILE__),
            'position' => [
            ],
        ]);
        $response = $responses[0];
        $this->assertInstanceOf(ResponseError::class, $response->responseError);
        $this->assertContains('No type locators', $response->responseError->message);
    }

    private function createTester(): ServerTester
    {
        $container = PhpactorContainer::fromExtensions([
            LoggingExtension::class,
            LanguageServerExtension::class,
            LanguageServerReferenceFinderExtension::class,
            ReferenceFinderExtension::class,
            FilePathResolverExtension::class,
        ]);
        
        $builder = $container->get(LanguageServerExtension::SERVICE_LANGUAGE_SERVER_BUILDER);
        $this->assertInstanceOf(LanguageServerBuilder::class, $builder);

        return $builder->buildServerTester();
    }
}
