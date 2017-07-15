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
namespace Annolazy\t\Model;

use Annolazy\Model\Doc;

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
class DocTest extends TestCase
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
        $construct = new Doc($this->getStub('full'));

        $this->assertInstanceOf(
            Doc::class,
            $construct
        );

        return $construct;
    }

    /**
     * @depends testConstruct
     */
    public function testGetShortDesc($c)
    {
        $this->assertEquals(
            'Short desc goes here.',
            $c->getShortDesc()
        );
    }
}
