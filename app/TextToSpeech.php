<?php

class TextToSpeech
{

    private $polly;

    public function __construct($polly)
    {
        $this->polly = $polly;
    }

    public function convert($text, $options = [])
    {
        $defaults = [
            'OutputFormat' => 'mp3',
            'TextType'     => 'ssml',
            'VoiceId'      => 'Salli',
        ];

        $options = array_merge($defaults, $options);

        // check for splitting text
        $text = preg_replace('/^\<\/\bspeak\b\>\s+\<\bspeak\b$/', '</speak>PARABLE_BREAK<speak', $text);

        $text_parts = explode('PARABLE_BREAK', $text);

        $audio = '';

        foreach ($text_parts as $snippet) {
            $snippet = str_replace('<speak>', '<speak version="1.1" xmlns="http://www.w3.org/2001/10/synthesis" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.w3.org/2001/10/synthesis http://www.w3.org/TR/speech-synthesis11/synthesis.xsd" xml:lang="en-US">', $snippet);

            $options['Text'] = '<?xml version="1.0"?>' . $snippet;

            $result = $this->polly->synthesizeSpeech($options);
            
            $audio .= $result->get('AudioStream')->getContents();
        }

        return $audio;
    }

}