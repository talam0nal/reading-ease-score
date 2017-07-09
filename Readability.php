<?php

namespace Talam0nal\Readability;

class Readability
{
    public $writing_sample;

    public function easeScore(string $writing_sample) : float
    {
        $this->writing_sample = $writing_sample;
         # Calculate score
         
         # Return of 0.0 to 100.0
         return 206.835 - (1.015 * $this->averageSentenceLength()) - (84.6 * $this->averageNumberOfSyllables());
    }

    private function averageSentenceLength() : float
    {
        return $this->numberOfWords()/$this->numberOfSentences();
    }

    private function numberOfWords() : int
    {
        return str_word_count($this->writing_sample);
    }

    private function numberOfSentences() : int
    {      
        return preg_match_all('/[^\s](\.|\!|\?|\;)(?!\w)/', $this->writing_sample, $match);
    }

    private function averageNumberOfSyllables() : float
    {
        return $this->syllables()/$this->numberOfWords();
    }

    private function syllables() : int
    {
        $syllableCount = 0;
        $words = explode(' ', $this->writing_sample);
        foreach ($words as $word) {
            $syllableCount += $this->countSyllableInWord($word);
        }
        return $syllableCount;
    }

    private function countSyllableInWord(string $word) : int
    {
        $pattern = new Pattern();
        $word = trim($word);

        if (isset($pattern->{'problem_words'}[$word])) {
            return $pattern->{'problem_words'}[$word];
        }

        $word = str_replace($pattern->{'prefix_and_suffix_patterns'}, '', $word, $suffixes);

        $wordParts = preg_split('`[^aeiouy]+`', $word);
        $wordPartCount = 0;
        foreach ($wordParts as $part) {
            if ($part !== '') {
                $wordPartCount++;
            }
        }
        $count = $wordPartCount + $suffixes;

        foreach ($pattern->{'subtract_syllable_patterns'} as $syllable) {
            $count -= preg_match('`' . $syllable . '`', $word);
        }

        foreach ($pattern->{'add_syllable_patterns'} as $syllable) {
            $count += preg_match('`' . $syllable . '`', $word);
        }

        $count = ($count == 0) ? 1 : $count;
        return $count;
    }

}