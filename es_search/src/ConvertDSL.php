<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/7/29
 * Time: 下午4:35
 */

namespace BaAGee\SimpleDsl;

/**
 * Class ConvertDSL
 * @package BaAGee\SimpleDsl
 */
class ConvertDSL
{
    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var array
     */
    protected $hlPreTags  = [];
    /**
     * @var array
     */
    protected $hlPostTags = [];

    /**
     * ConvertDSL constructor.
     * @param BoolQuery $boolQuery
     */
    public function __construct(BoolQuery $boolQuery)
    {
        $this->addQuery($boolQuery);
    }

    /**
     * @param BoolQuery $boolQuery
     * @return $this
     */
    public function addQuery(BoolQuery $boolQuery)
    {
        $this->query['query'] = $boolQuery->getDslArray();
        return $this;
    }

    /**
     * @param $startTag
     * @param $endTag
     * @return $this
     */
    public function setHighlightTag($startTag, $endTag)
    {
        $this->hlPreTags[] = $startTag;
        $this->hlPostTags[] = $endTag;
        return $this;
    }

    /**
     * @param bool $pretty
     * @return string
     */
    public function getDslJson($pretty = false)
    {
        $flag = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if ($pretty) {
            $flag |= JSON_PRETTY_PRINT;
        }
        return json_encode($this->query, $flag);
    }

    /**
     * @param $sortArr
     * @return $this
     */
    public function sort($sortArr)
    {
        foreach ($sortArr as $key => $sort) {
            $this->query['sort'][$key] = ['order' => $sort];
        }
        return $this;
    }

    /**
     * @param array $fieldsArr
     * @return $this
     */
    public function sourceInclude(array $fieldsArr)
    {
        $this->query['_source']['includes'] = array_merge((array)($this->query['_source']['includes'] ?? []), $fieldsArr);
        return $this;
    }

    /**
     * @param array $fieldsArr
     * @return $this
     */
    public function sourceExcludes(array $fieldsArr)
    {
        $this->query['_source']['excludes'] = array_merge((array)($this->query['_source']['excludes'] ?? []), $fieldsArr);
        return $this;
    }

    /**
     * @param $fields
     * @return $this
     */
    public function highlight($fields)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        foreach ($fields as $field) {
            $this->query['highlight']['fields'][$field] = new \stdClass();
        }
        if (!empty($this->hlPreTags) && !empty($this->hlPostTags)) {
            $this->query['highlight']['pre_tags'] = $this->hlPreTags;
            $this->query['highlight']['post_tags'] = $this->hlPostTags;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDslJson();
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset = 0)
    {
        $this->query['from'] = $offset;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit = 100)
    {
        $this->query['size'] = $limit;
        return $this;
    }
}
