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
     * ConvertDSL constructor.
     * @param BoolQuery $boolQuery
     */
    public function __construct(BoolQuery $boolQuery = null)
    {
        if ($boolQuery instanceof BoolQuery) {
            $this->addQuery($boolQuery);
        }
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
     * 获取dsl json
     * @param bool $pretty 是否格式化
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
     * 排序
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
     * 查询某些字段
     * @param array $fieldsArr
     * @return $this
     */
    public function sourceInclude(array $fieldsArr)
    {
        $this->query['_source']['includes'] = array_merge((array)($this->query['_source']['includes'] ?? []), $fieldsArr);
        return $this;
    }

    /**
     * 排除某些字段
     * @param array $fieldsArr
     * @return $this
     */
    public function sourceExcludes(array $fieldsArr)
    {
        $this->query['_source']['excludes'] = array_merge((array)($this->query['_source']['excludes'] ?? []), $fieldsArr);
        return $this;
    }

    /**
     * 高亮
     * @param          $fields
     * @param string[] $hlPreTags
     * @param string[] $hlPostTags
     * @return $this
     */
    public function highlight($fields, $hlPreTags = ['<em>'], $hlPostTags = ['</em>'])
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        foreach ($fields as $field) {
            $this->query['highlight']['fields'][$field] = new \stdClass();
        }
        $this->query['highlight']['pre_tags'] = $hlPreTags;
        $this->query['highlight']['post_tags'] = $hlPostTags;
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
     * 分页 offset
     * @param int $offset
     * @return $this
     */
    public function offset($offset = 0)
    {
        $this->query['from'] = (int)$offset;
        return $this;
    }

    /**
     * 分页 limit
     * @param int $limit
     * @return $this
     */
    public function limit($limit = 100)
    {
        $this->query['size'] = (int)$limit;
        return $this;
    }

    /**
     * @param array $searchAfter
     * @return $this
     * @throws \Exception
     */
    public function searchAfter(array $searchAfter)
    {
        if (empty($this->query['sort'])) {
            throw new \Exception('请先设置好排序 orderBy');
        }
        $searchAfter = (array)$searchAfter;
        if (!empty($searchAfter)) {
            $this->query['search_after'] = $searchAfter;
        } else {
            unset($this->query['search_after']);
        }
        return $this;
    }
}
