<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/7/29
 * Time: 下午4:35
 */

namespace BaAGee\SimpleDsl;
/**
 * Class BoolQuery
 * @package BaAGee\SimpleDsl
 */
class BoolQuery
{
    /**
     * @var array
     */
    protected $query = [];

    //===========================must==========================

    /**
     * 等于 =
     * @param $key
     * @param $val
     * @return $this
     */
    public function mustTerm($key, $val)
    {
        $this->query['bool']['filter']['bool']['must'][] = ['term' => [$key => $val]];
        return $this;
    }

    /**
     * in
     * @param       $key
     * @param array $valArr
     * @return $this
     */
    public function mustTerms($key, array $valArr)
    {
        $this->query['bool']['filter']['bool']['must'][] = ['terms' => [$key => $valArr]];
        return $this;
    }

    /**
     * like
     * @param $key
     * @param $val
     * @return $this
     */
    public function mustLike($key, $val)
    {
        $this->query['bool']['filter']['bool']['must'][] = ['wildcard' => [$key => $val]];
        return $this;
    }

    /**
     * 分词匹配
     * @param        $key
     * @param        $val
     * @param string $operator
     * @return $this
     */
    public function mustMatch($key, $val, $operator = 'or')
    {
        $this->query['bool']['must'][] = ['match' => [$key => ['query' => $val, 'operator' => $operator]]];
        return $this;
    }

    /**
     * 多个字段同时匹配
     * @param array  $fields
     * @param        $val
     * @param string $type
     * @return $this
     */
    public function mustMultiMatch(array $fields, $val, $type = 'best_fields')
    {
        $this->query['bool']['must'][] = ['multi_match' => ['query' => $val, 'fields' => $fields, 'type' => $type]];
        return $this;
    }

    /**
     * 范围查询
     * @param       $key
     * @param array $operatorToVal
     * @return $this
     */
    public function mustRange($key, array $operatorToVal)
    {
        // $this->query['bool']['must'][] = ['range' => [$key => $operatorToVal]];
        $this->query['bool']['filter']['bool']['must'][] = ['range' => [$key => $operatorToVal]];
        return $this;
    }

    /**
     * 嵌套一个bool查询
     * @param BoolQuery $boolQuery
     * @return $this
     */
    public function mustBoolQuery(BoolQuery $boolQuery)
    {
        $this->query['bool']['filter']['bool']['must'][] = $boolQuery->getDslArray();
        return $this;
    }

    //===========================should==========================

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function shouldTerm($key, $val)
    {
        $this->query['bool']['filter']['bool']['should'][] = ['term' => [$key => $val]];
        return $this;
    }

    /**
     * @param       $key
     * @param array $valArr
     * @return $this
     */
    public function shouldTerms($key, array $valArr)
    {
        $this->query['bool']['filter']['bool']['should'][] = ['terms' => [$key => $valArr]];
        return $this;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function shouldLike($key, $val)
    {
        $this->query['bool']['filter']['bool']['should'][] = ['wildcard' => [$key => $val]];
        return $this;
    }

    /**
     * @param        $key
     * @param        $val
     * @param string $operator
     * @return $this
     */
    public function shouldMatch($key, $val, $operator = 'or')
    {
        $this->query['bool']['should'][] = ['match' => [$key => ['query' => $val, 'operator' => $operator]]];
        return $this;
    }


    /**
     * @param array  $fields
     * @param        $val
     * @param string $type
     * @return $this
     */
    public function shouldMultiMatch(array $fields, $val, $type = 'best_fields')
    {
        $this->query['bool']['should'][] = ['multi_match' => ['query' => $val, 'fields' => $fields, 'type' => $type]];
        return $this;
    }

    /**
     * @param       $key
     * @param array $operatorToVal
     * @return $this
     */
    public function shouldRange($key, array $operatorToVal)
    {
        // $this->query['bool']['should'][] = ['range' => [$key => $operatorToVal]];
        $this->query['bool']['filter']['bool']['should'][] = ['range' => [$key => $operatorToVal]];
        return $this;
    }

    /**
     * @param BoolQuery $boolQuery
     * @return $this
     */
    public function shouldBoolQuery(BoolQuery $boolQuery)
    {
        $this->query['bool']['filter']['bool']['should'][] = $boolQuery->getDslArray();
        return $this;
    }

    //===========================not==========================

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function notTerm($key, $val)
    {
        $this->query['bool']['filter']['bool']['must_not'][] = ['term' => [$key => $val]];
        return $this;
    }

    /**
     * @param       $key
     * @param array $valArr
     * @return $this
     */
    public function notTerms($key, array $valArr)
    {
        $this->query['bool']['filter']['bool']['must_not'][] = ['terms' => [$key => $valArr]];
        return $this;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function notLike($key, $val)
    {
        $this->query['bool']['filter']['bool']['must_not'][] = ['wildcard' => [$key => $val]];
        return $this;
    }

    /**
     * @param        $key
     * @param        $val
     * @param string $operator
     * @return $this
     */
    public function notMatch($key, $val, $operator = 'or')
    {
        $this->query['bool']['must_not'][] = ['match' => [$key => ['query' => $val, 'operator' => $operator]]];
        return $this;
    }

    /**
     * @param array  $fields
     * @param        $val
     * @param string $type
     * @return $this
     */
    public function notMultiMatch(array $fields, $val, $type = 'best_fields')
    {
        $this->query['bool']['must_not'][] = ['multi_match' => ['query' => $val, 'fields' => $fields, 'type' => $type]];
        return $this;
    }

    /**
     * @param       $key
     * @param array $operatorToVal
     * @return $this
     */
    public function notRange($key, array $operatorToVal)
    {
        // $this->query['bool']['must_not'][] = ['range' => [$key => $operatorToVal]];
        $this->query['bool']['filter']['bool']['must_not'][] = ['range' => [$key => $operatorToVal]];
        return $this;
    }

    /**
     * @param BoolQuery $boolQuery
     * @return $this
     */
    public function notBoolQuery(BoolQuery $boolQuery)
    {
        $this->query['bool']['filter']['bool']['must_not'][] = $boolQuery->getDslArray();
        return $this;
    }

    // ========================================================

    /**
     * @param bool $pretty
     * @return string
     */
    public function getDslJson($pretty = false)
    {
        $flag = JSON_UNESCAPED_UNICODE;
        if ($pretty) {
            $flag |= JSON_PRETTY_PRINT;
        }
        return json_encode($this->query, $flag);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDslJson();
    }

    /**
     * @return array
     */
    public function getDslArray()
    {
        return $this->query;
    }
}
