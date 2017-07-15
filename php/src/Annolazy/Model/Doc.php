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
namespace Annolazy\Model;

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
class Doc
{
    private $lines;

    public function __construct(string $comment)
    {
        // Clean out doc stuff we don't need.
        $lines = explode("\n", $comment);

        foreach ($lines as &$line) {
            $line = preg_replace('/^[\/\*\s]*/', '', $line);
        }

        $this->lines = $lines;
    }

    public function getIndex(int $init = 0)
    {
        // The short desc is everything up until we hit the first empty line.
        $start  = $init;
        $length = 0;

        for ($i = $init; $i < count($this->lines); $i++) {
            $line = $this->lines[$i];

            // End position is found when we hit an empty line
            // after we have found a start position.
            if (0 === strlen(trim($line)) && $start > $init) {
                break;
            }

            // Or, if we have found a line starting as a tag.
            if (0 === strpos(trim($line), '@')) {
                break;
            }

            if (0 < strlen(trim($line)) && $start === $init) {
                $start = $i;
            }

            $length++;
        }

        return [$start, $length - 1];
    }

    public function getShortDesc()
    {
        list($start, $end) = $this->getIndex();

        return trim(implode(' ', array_slice($this->lines, $start, $end)));
    }

    public function getLongDesc()
    {
        // First hit will be short, next will be long.
        list($start, $end) = $this->getIndex();

        list($start, $end) = $this->getIndex($start + $end);

        return trim(implode(' ', array_slice($this->lines, $start, $end)));
    }
}
