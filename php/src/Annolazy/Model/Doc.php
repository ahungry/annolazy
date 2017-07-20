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

    /**
     * Create a Doc, given some user docblock.
     *
     * The lines are used throughout further method calls.
     *
     * @param string $comment The user docblock.
     *
     * @return void
     */
    public function __construct(string $comment)
    {
        // Clean out doc stuff we don't need.
        $lines = explode("\n", $comment);

        foreach ($lines as &$line) {
            $line = preg_replace('/^[\/\*\s]*/', '', $line);
        }

        $this->lines = $lines;
    }

    /**
     * Seek the start/end positions of line elements.
     *
     * When setting the short/long descriptions, we need to find this index,
     * so we know where one ends and the other resumes.
     *
     * @param integer $init Initial position to start seeking across lines.
     *
     * @return array An array, of [start, end] positions.
     */
    public function getIndex(int $init = 0): array
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

    /**
     * Query out the short description.
     *
     * @return string
     */
    public function getShortDesc(): string
    {
        list($start, $end) = $this->getIndex();

        return trim(implode(' ', array_slice($this->lines, $start, $end)));
    }

    /**
     * Query out the long description.
     *
     * @return string
     */
    public function getLongDesc(): string
    {
        // First hit will be short, next will be long.
        list($start, $end) = $this->getIndex();
        list($start, $end) = $this->getIndex($start + $end);

        $desc = trim(implode(' ', array_slice($this->lines, $start, $end)));

        // Accidentally grabbed a tag here
        if (0 === strpos($desc, '@')) {
            $desc = '';
        }

        return $desc;
    }

    /**
     * Query out a specific param.
     *
     * @param string $name The param name to query.
     *
     * @return array
     */
    public function getParam(string $name): array
    {
        $param = ['type' => null, 'desc' => null];

        $find = preg_match(
            '/@param\s+(\S*?)\s+\$' . $name . '\s+(.*?)(\x00@|$)/',
            implode(chr(0), $this->lines),
            $m
        );

        if ($find) {
            $param = [
                'type' => trim($m[1]),
                'desc' => trim(preg_replace('/\x00/', ' ', $m[2])),
            ];
        }

        // We didn't find a proper param annotation with format going in
        // param:type:name:desc format
        if (!$find) {
            $find = preg_match(
                '/@param\s+\$' . $name . '\s+(.*?)(\x00@|$)/',
                implode(chr(0), $this->lines),
                $m
            );

            if ($find) {
                $param = [
                    'type' => 'mixed',
                    'desc' => trim(preg_replace('/\x00/', ' ', $m[1])),
                ];
            }
        }

        return $param;
    }

    /**
     * Query out the return tag.
     *
     * @return array
     */
    public function getReturn(): array
    {
        $param = ['type' => 'mixed', 'desc' => null];

        $find = preg_match(
            '/@return\s+(\S*?)\s+(.*?)(\x00@|$)/',
            implode(chr(0), $this->lines),
            $m
        );

        if ($find) {
            $param = [
                'type' => trim($m[1]),
                'desc' => trim(preg_replace('/\x00/', ' ', $m[2])),
            ];
        }

        // We didn't find a proper param annotation with format going in
        // return:type:desc format
        if (!$find) {
            $find = preg_match(
                '/@return\s+(\S*?)(\x00@|$)/',
                implode(chr(0), $this->lines),
                $m
            );

            if ($find) {
                $param = [
                    'type' => trim($m[1]),
                    'desc' => trim(preg_replace('/\x00/', ' ', $m[2])),
                ];
            }
        }

        return $param;
    }

    /**
     * For tags that are not param/return, build them out separately.
     *
     * @return array
     */
    public function getUserTags(): array
    {
        $tags = explode('@', implode("\n", $this->lines));
        $tags = array_map('trim', $tags);

        if (empty($tags)) {
            return [];
        }

        // First tag may or may not be a short/long desc, so we can try to see
        // if it matches, and remove if so.
        $short = preg_replace('/\s/', '', $this->getShortDesc());
        $tagShort = preg_replace('/\s/', '', $tags[0]);

        if ($short) {
            if (0 === strpos($tagShort, $short)) {
                array_shift($tags);
            }
        }

        // Also, we don't need any param annotations, we get automatically
        // later.
        $tags = array_filter(
            $tags,
            function ($tag) {
                return 0 !== strpos($tag, 'param ')
                    && 0 !== strpos($tag, 'return ');
            }
        );

        return $tags;
    }
}
