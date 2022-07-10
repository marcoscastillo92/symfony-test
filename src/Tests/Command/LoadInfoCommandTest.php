<?php
namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class LoadInfoCommandTest extends KernelTestCase
{
    protected CommandTester $commandTester;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:load-info');
        $this->commandTester = new CommandTester($command);
    }

    public function testValidDataShouldReturnSuccess()
    {
        $result = $this->commandTester->execute([
            'path' => 'public\data\Test_valid.csv',
        ]);

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertEquals(Command::SUCCESS, $result, 'Expected to success');
        $this->assertStringContainsString('SUCCESS',  $this->commandTester->getDisplay());
    }

    public function testInvalidDateShouldReturnInvalid()
    {
        $result = $this->commandTester->execute([
            'path' => 'public\data\Test_invalid.csv',
        ]);

        $this->assertEquals(Command::INVALID, $result, 'Expected to be invalid');
        $this->assertStringContainsString('INVALID DATA', $this->commandTester->getDisplay());
    }
}