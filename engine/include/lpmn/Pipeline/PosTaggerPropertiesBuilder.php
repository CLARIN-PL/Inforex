<?php

namespace Inforex\Lpmn\Pipeline;

class PosTaggerPropertiesBuilder
{
    /** @var array<string, mixed> */
    private $json = array();

    public function language($language)
    {
        $this->json['lang'] = $language;

        return $this;
    }

    public function tagset($tagset)
    {
        $this->json['tagset'] = $tagset;

        return $this;
    }

    public function methodNer()
    {
        $this->json['method'] = 'ner';

        return $this;
    }

    public function methodTagger()
    {
        $this->json['method'] = 'tagger';

        return $this;
    }

    public function nerType()
    {
        $this->json['ner_type'] = 'liner';

        return $this;
    }

    public function taggerType($taggerType)
    {
        $this->json['tagger_type'] = $taggerType;

        return $this;
    }

    public function linking($linking)
    {
        $this->json['linking'] = $linking;

        return $this;
    }

    public function linkingSearchType($searchType)
    {
        if (!isset($this->json['linking_options']) || !is_array($this->json['linking_options'])) {
            $this->json['linking_options'] = array();
        }

        $this->json['linking_options']['search_type'] = $searchType;

        return $this;
    }

    public function outputFormat($outputFormat)
    {
        $this->json['output_format'] = $outputFormat;

        return $this;
    }

    public function build()
    {
        return new PosTaggerProperties($this->json);
    }
}
