<?php

namespace Inforex\Lpmn\Result;

class JsonlMerger
{
    public static function mergeIfNeeded($payload)
    {
        if (!is_string($payload) || $payload === '') {
            return $payload;
        }

        $decoded = json_decode($payload, true);
        if (is_array($decoded)) {
            return $payload;
        }

        $lines = preg_split('/\R/u', $payload);
        if (!is_array($lines) || count($lines) < 2) {
            return $payload;
        }

        $chunks = array();
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $chunk = json_decode($line, true);
            if (!is_array($chunk)) {
                return $payload;
            }

            $chunks[] = $chunk;
        }

        if (count($chunks) < 2) {
            return $payload;
        }

        return json_encode(self::mergeChunks($chunks), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array<int, array<string, mixed>> $chunks
     * @return array<string, mixed>
     */
    public static function mergeChunks(array $chunks)
    {
        $merged = array_shift($chunks);
        if ($merged === null) {
            return array();
        }

        $merged['text'] = isset($merged['text']) ? (string) $merged['text'] : '';
        $merged['tokens'] = self::normalizeLayers(isset($merged['tokens']) ? $merged['tokens'] : array());
        $merged['spans'] = self::normalizeLayers(isset($merged['spans']) ? $merged['spans'] : array());

        foreach ($chunks as $chunk) {
            $offset = mb_strlen($merged['text'], 'utf-8');
            $chunkText = isset($chunk['text']) ? (string) $chunk['text'] : '';

            $merged['text'] .= $chunkText;
            $merged['tokens'] = self::mergeLayerGroups($merged['tokens'], self::normalizeLayers(isset($chunk['tokens']) ? $chunk['tokens'] : array()), $offset);
            $merged['spans'] = self::mergeLayerGroups($merged['spans'], self::normalizeLayers(isset($chunk['spans']) ? $chunk['spans'] : array()), $offset);

            foreach ($chunk as $key => $value) {
                if (in_array($key, array('text', 'tokens', 'spans'), true)) {
                    continue;
                }

                if (!array_key_exists($key, $merged) || $merged[$key] === '' || $merged[$key] === null) {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * @param mixed $layers
     * @return array<string, array<int, array<string, mixed>>>
     */
    private static function normalizeLayers($layers)
    {
        return is_array($layers) ? $layers : array();
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $baseLayers
     * @param array<string, array<int, array<string, mixed>>> $nextLayers
     * @return array<string, array<int, array<string, mixed>>>
     */
    private static function mergeLayerGroups(array $baseLayers, array $nextLayers, $offset)
    {
        foreach ($nextLayers as $layerName => $items) {
            if (!isset($baseLayers[$layerName]) || !is_array($baseLayers[$layerName])) {
                $baseLayers[$layerName] = array();
            }

            foreach ($items as $item) {
                $baseLayers[$layerName][] = self::shiftOffsets($item, $offset);
            }
        }

        return $baseLayers;
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private static function shiftOffsets(array $item, $offset)
    {
        if (isset($item['start']) && is_numeric($item['start'])) {
            $item['start'] = (int) $item['start'] + $offset;
        }

        if (isset($item['stop']) && is_numeric($item['stop'])) {
            $item['stop'] = (int) $item['stop'] + $offset;
        }

        return $item;
    }
}
