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

    public function getIndexShortDesc()
    {
        // The short desc is everything up until we hit the first empty line.
        $start = 0;
        $end   = 0;

        for ($i = 0; $i < count($this->lines); $i++) {
            $line = $this->lines[$i];

            // End position is found when we hit an empty line
            // after we have found a start position.
            if (0 === strlen(trim($line)) && $start > 0) {
                break;
            }

            if (0 < strlen(trim($line)) && $start === 0) {
                $start = $i;
            }

            $end = $i;
        }

        return [$start, $end];
    }

    public function getShortDesc()
    {
        list($start, $end) = $this->getIndexShortDesc();

        return trim(implode(' ', array_slice($this->lines, $start, 1+$end - $start)));
    }

    public function getIndexLongDesc()
    {
        // First, find the short (we can't have a long desc unless the short
        // one exists).
        list($start, $endS) = $this->getIndexShortDesc();

        // The short desc is everything up until we hit the first empty line.
        $start = 0;
        $end   = 0;

        for ($i = $endS + 1; $i < count($this->lines); $i++) {
            $end = $i;
            $line = $this->lines[$i];

            // End position is found when we hit an empty line
            // after we have found a start position.
            if (0 === strlen(trim($line)) && $start > 0) {
                break;
            }

            // Or, if we have found a line starting as a tag.
            if (0 === strpos(trim($line), '@')) {
                break;
            }

            if (0 < strlen(trim($line)) && $start === 0) {
                $start = $i;
            }
        }
        //var_dump ($start, $end); die;


        return [$start, $end];
    }

    public function getLongDesc()
    {
        list($start, $end) = $this->getIndexLongDesc();

        return trim(implode(' ', array_slice($this->lines, $start, 1+$end - $start)));
    }
}
