<?php
/**
 * Auto-generate documentation for a user.
 *
 * Who wants to waste time writing out what can be inferred?
 *
 * PHP version 7+
 *
 * @category Laziness
 * @package  Annolazy
 * @author   Matthew Carter <m@ahungry.com>
 * @license  AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 * @link     http://ahungry.com
 */
namespace Annolazy\t\Service;

use Annolazy\Model\Doc;
use Annolazy\Service\DocGenerator;

use PHPUnit\Framework\TestCase;

/**
 * Auto-generate documentation for a user.
 *
 * Who wants to waste time writing out what can be inferred?
 *
 * PHP version 7+
 *
 * @category Laziness
 * @package  Annolazy
 * @author   Matthew Carter <m@ahungry.com>
 * @license  AGPLv3 https://www.gnu.org/licenses/agpl-3.0.html
 * @link     http://ahungry.com
 */
class DocGeneratorTest extends TestCase
{
    /**
     * @todo add doc
     */
    public function getStub($name)
    {
        return file_get_contents(
            __DIR__ . '/../fixtures/stub/doc/' . $name . '.txt'
        );
    }

    public function testConstruct()
    {
        $construct = new DocGenerator();

        $this->assertInstanceOf(
            DocGenerator::class,
            $construct
        );

        return $construct;
    }

    /**
     * @depends testConstruct
     */
    public function testGenerateMethodComment($c)
    {
        $methodData = [
            'params' => [
                [
                    'name' => '$fakeName',
                    'type' => 'fake',
                    'desc' => 'bla',
                ],
            ],
        ];
        $userComment = $this->getStub('badParams');
        $comment = $c->generateMethodComment($methodData, $userComment);
        $expected = $this->getStub('badParamsExpected');
        $this->assertEquals($expected, $comment);
    }
}