<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * @group strictTypeTest
 */
class StrictnessTest extends TestCase
{
    /**
     * @var string[]
     */
    private array $foldersToCheck = [
        './src',
        './tests'
    ];

    private Finder $finder;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->finder = new Finder();
        $this->finder->files()->name('*.php');
    }

    /**
     * @coversNothing
     * @return void
     */
    public function testAllPhpFilesAreDeclaredStrict(): void
    {
        $phpContentStart = <<<CONTENT
        <?php\n\ndeclare(strict_types=1);\n\n
        CONTENT;

        foreach ($this->foldersToCheck as $folder) {
            $this->finder->in($folder);

            foreach ($this->finder as $phpFile) {
                $this->assertStringStartsWith(
                    $phpContentStart,
                    substr($phpFile->getContents(), 0, 40),
                    sprintf(
                        'File %s/%s is not declared strict!',
                        $phpFile->getPath(),
                        $phpFile->getFilename()
                    )
                );
            }
        }
    }
}
