<?php
namespace Fixture\BaseExtension\Domain\Model;

class AnotherBlob extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $property = '';

    /**
     * Getter for property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Setter for property
     *
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }
}
