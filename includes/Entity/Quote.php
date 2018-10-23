<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/23/2018
 * Time: 9:58 PM
 */

namespace MaratMSBootcampPlugin\Entity;


class Quote
{
    /**
     * @var int
     */
    private $id = 0;

    /**
     * @var int
     */
    private $ownerAppId = 0;

    /**
     * @var int
     */
    private $authorId = 0;

    /**
     * @var string
     */
    private $authorName = "";

    /**
     * @var string
     */
    private $text = "";

    /**
     * @param array $dataArray
     * @return Quote
     */
    public static function constructFromArray($dataArray)
    {
        $quote = new static();
        $quote->arrayExchange($dataArray);
        return $quote;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Quote
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getOwnerAppId()
    {
        return $this->ownerAppId;
    }

    /**
     * @param int $ownerAppId
     * @return Quote
     */
    public function setOwnerAppId($ownerAppId)
    {
        $this->ownerAppId = $ownerAppId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     * @return Quote
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     * @return Quote
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Quote
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param array $dataArray
     * @return Quote
     */
    public function arrayExchange($dataArray)
    {
        $this
            ->setId(isset($dataArray['id']) ? $dataArray['id'] : 0)
            ->setOwnerAppId(isset($dataArray['ownerAppId']) ? $dataArray['ownerAppId'] : 0)
            ->setAuthorId(isset($dataArray['authorId']) ? $dataArray['authorId'] : 0)
            ->setAuthorName(isset($dataArray['authorName']) ? $dataArray['authorName'] : "")
            ->setText(isset($dataArray['text']) ? $dataArray['text'] : "")
        ;
        return $this;
    }

}



