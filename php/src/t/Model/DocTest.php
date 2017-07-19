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
        $construct = new \stdClass($this->getStub('full'));

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

    /**
     * @depends testConstruct
     */
    public function testGetLongDesc($c)
    {
        $this->assertEquals(
            'Long desc goes here.',
            $c->getLongDesc()
        );
    }

    /**
     * @depends testConstruct
     */
    public function testGetParam($c)
    {
        $this->assertEquals(
            'Type',
            $c->getParam('name')['type']
        );

        $this->assertEquals(
            'Some description',
            $c->getParam('name')['desc']
        );

        $this->assertEquals(
            'TypeTwo',
            $c->getParam('nameTwo')['type']
        );

        $this->assertEquals(
            'Some other description that seems to wrap.',
            $c->getParam('nameTwo')['desc']
        );

        $this->assertEquals(
            'TypeThree',
            $c->getParam('nameThree')['type']
        );

        $this->assertEquals(
            'Some other description that wraps without star.',
            $c->getParam('nameThree')['desc']
        );
    }

    /**
     * @depends testConstruct
     */
    public function testGetReturn($c)
    {
        $this->assertEquals(
            'Foo',
            $c->getReturn()['type']
        );

        $this->assertEquals(
            'My return description',
            $c->getReturn()['desc']
        );
    }

    /**
     * @depends testConstruct
     */
    public function testGetUserTags($c)
    {
        $tags = $c->getUserTags();
        $this->assertContains(
            'todo Another todo goes here',
            $tags
        );
    }
}
