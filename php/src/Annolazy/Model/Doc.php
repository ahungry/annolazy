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

    public function getShortDesc()
    {
        // The short desc is everything up until we hit the first empty line.
        $desc = [];

        foreach ($this->lines as $line) {
            if (0 === strlen(trim($line)) && count($desc) > 0) {
                break;
            }

            $desc[] = $line;
        }

        return trim(implode(' ', $desc));
    }
}
