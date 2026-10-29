<?php

/** Shared assertions for the supported, explicitly selected export formats. */
trait CorpusExportAssertions
{
    public static function documentFormats(): array
    {
        return array(
            'CCL XML' => array(ConllAndJsonFactory::FORMAT_LEGACY),
            'Text and metadata' => array(ConllAndJsonFactory::FORMAT_TEXT),
            'CoNLL-U CLARIN' => array(ConllAndJsonFactory::FORMAT_CONLLU_CLARIN),
            'CLARIN JSON' => array(ConllAndJsonFactory::FORMAT_CLARIN_JSON),
        );
    }

    public static function annotationFormats(): array
    {
        return array('CCL XML' => array(ConllAndJsonFactory::FORMAT_LEGACY), 'CLARIN JSON' => array(ConllAndJsonFactory::FORMAT_CLARIN_JSON));
    }

    private function assertAnnotationLemmas($path, array $expected)
    {
        $json = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $actual = array();
        foreach ($json['spans'] as $layer => $definition) {
            if ($layer === 'sentence') {
                continue;
            }
            foreach ($definition['items'] as $span) {
                $actual[$span['id']] = array('start' => $span['start'], 'stop' => $span['stop'], 'type' => $span['type'], 'lemma' => $span['lemma'] ?? null);
            }
        }
        $this->assertSame($expected, $actual);
    }

    private function assertDocumentExport($format, $path, $reportId, $report, SimpleCcl $fixture, array $annotations = array(), array $relations = array())
    {
        $metadata = array('id' => $reportId);
        foreach (array('date', 'title', 'source', 'author', 'tokenization') as $key) {
            $metadata[$key] = $report[$key];
        }
        $metadata['subcorpus'] = '';
        $expectedFiles = array();
        if ($format === ConllAndJsonFactory::FORMAT_TEXT) {
            $expectedFiles = array('.txt', '.ini');
            $this->assertSame($report['content'], file_get_contents($path . '.txt'));
            $this->assertEquals(array('metadata' => $metadata), parse_ini_string(file_get_contents($path . '.ini'), true, INI_SCANNER_RAW));
        } elseif ($format === ConllAndJsonFactory::FORMAT_CONLLU_CLARIN) {
            $expectedFiles = array('.conllu');
            $this->assertSame($fixture->toCONLL(), file_get_contents($path . '.conllu'));
        } elseif ($format === ConllAndJsonFactory::FORMAT_LEGACY) {
            $expectedFiles = array('.xml', '.rel.xml');
            $xml = simplexml_load_file($path . '.xml');
            $this->assertSame('document', $xml->getName());
            $this->assertEquals(simplexml_load_string($fixture->toXML()), $xml->chunkList);
            $exportedMetadata = array();
            foreach ($xml->metadata->prop as $prop) {
                $exportedMetadata[(string)$prop['key']] = (string)$prop;
            }
            $this->assertEquals($metadata, $exportedMetadata);
            $this->assertSame("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE relations SYSTEM \"ccl-rel.dtd\">\n<relations>\n</relations>\n", file_get_contents($path . '.rel.xml'));
        } elseif ($format === ConllAndJsonFactory::FORMAT_CLARIN_JSON) {
            $expectedFiles = array('.json');
            $json = json_decode(file_get_contents($path . '.json'), true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame(str_pad($reportId, 8, '0', STR_PAD_LEFT), $json['id']);
            $this->assertSame($report['title'], $json['label']);
            $this->assertSame($report['content'], $json['text']);
            $expectedTokens = array();
            foreach ($fixture->getTokens() as $index => $token) {
                $lexemes = array();
                foreach ($token['tags'] as $tag) {
                    $lexemes[] = array('lemma' => $tag['base_text'], 'pos' => $tag['ctag'], 'disamb' => (bool)$tag['disamb']);
                }
                $expectedTokens[] = array('lexemes' => $lexemes, 'id' => $index + 1, 'start' => $token['from'], 'stop' => $token['to'] + 1);
            }
            $this->assertSame($expectedTokens, $json['tokens']['default']);
            $expectedSpans = array();
            foreach ($annotations as $annotation) {
                $span = array(
                    'id' => (int)$annotation['id'],
                    'start' => $this->plainOffset($report['content'], $annotation['from']),
                    'stop' => $this->plainOffset($report['content'], $annotation['to']) + 1,
                    'type' => $annotation['type'],
                );
                if (isset($annotation['lemma'])) {
                    $span['lemma'] = $annotation['lemma'];
                }
                $expectedSpans[] = $span;
            }
            $this->assertSame($expectedSpans, $json['spans']['annotations']['items'] ?? array());
            $expectedRelations = array();
            foreach ($relations as $relation) {
                $expectedRelations[$relation['rsname']]['items'][] = array('id' => (int)$relation['id'], 'type' => $relation['name'], 'source' => (int)$relation['source_id'], 'target' => (int)$relation['target_id'], 'relation_type_id' => (int)$relation['relation_type_id']);
            }
            $this->assertSame(array_keys($expectedRelations), array_keys($json['relations']));
            foreach ($expectedRelations as $layer => $definition) {
                $this->assertSame($definition['items'], $json['relations'][$layer]['items']);
            }
        } else {
            $this->fail('Unhandled test export format: ' . $format);
        }
        foreach (array('.xml', '.rel.xml', '.txt', '.ini', '.json', '.conllu', '.conll') as $extension) {
            $this->assertSame(in_array($extension, $expectedFiles, true), file_exists($path . $extension), $format . ': ' . $extension);
        }
    }

    private function plainOffset($text, $compactOffset)
    {
        preg_match_all('/\S/u', $text, $matches, PREG_OFFSET_CAPTURE);
        return mb_strlen(substr($text, 0, $matches[0][$compactOffset][1]), 'UTF-8');
    }
}
