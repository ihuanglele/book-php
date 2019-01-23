<?php
/**
 * Created by PhpStorm.
 * Author: 晃晃<wangchunhui@doweidu.com>
 * Date: 2019-01-23
 * Time: 14:27
 */

namespace app\lib\book;


use Tightenco\Collect\Contracts\Support\Arrayable;

class BookEntry implements Arrayable
{

    // 书名
    private $name = '';
    // 作者
    private $author = '';
    // 封面
    private $cover = '';
    // 章节
    private $chapters = [];

    // 类型
    private $type = '';
    // id
    private $bookId = '';

    /**
     * @return string
     */
    public function getName()
    : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAuthor()
    : string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getCover()
    : string
    {
        return $this->cover;
    }

    /**
     * @param string $cover
     */
    public function setCover(string $cover)
    {
        $this->cover = $cover;
    }

    /**
     * @return array
     */
    public function getChapters()
    : array
    {
        return $this->chapters;
    }

    /**
     * @param array $chapters
     */
    public function setChapters(array $chapters)
    {
        $this->chapters = $chapters;
    }

    /**
     * @return string
     */
    public function getType()
    : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getBookId()
    : string
    {
        return $this->bookId;
    }

    /**
     * @param string $bookId
     */
    public function setBookId(string $bookId)
    {
        $this->bookId = $bookId;
    }


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name'     => $this->name,
            'author'   => $this->author,
            'cover'    => $this->cover,
            'chapters' => $this->chapters,
            'type'     => $this->type,
            'bookId'   => $this->bookId,
        ];
    }
}