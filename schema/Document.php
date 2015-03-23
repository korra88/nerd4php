<?php

namespace NERD\schema;

/**
 * Description of Document
 *
 * @author emanuele
 */
class Document {
    /**
     * Document identifier
     * @var integer
     */
    public $idDocument;
    
    public $text;
    /**
     * Document language abbreviation (en, it, fr...)
     * @var string
     */
    public $language;
    
    /**
     * Document type (plaintext | ...)
     * @var string
     */
    public $type;
    
}
