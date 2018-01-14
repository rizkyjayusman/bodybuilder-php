<?php

namespace Best\ElasticSearch\BodyBuilder\Test;

use Best\ElasticSearch\BodyBuilder\FilterAndQueryBuilder;
use Best\ElasticSearch\BodyBuilder\QueryBuilderTrait;
use Best\ElasticSearch\BodyBuilder\UtilTrait;

class QueryBuilderClass {
    use QueryBuilderTrait, UtilTrait;
}

function queryBuilder() {
    return new QueryBuilderClass();
}

class QueryBuilderTraitTest extends BaseTestCase
{
    public function testQueryBuilderMatchAll()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match_all');
        $this->assertEquals($result->getQuery(), array("match_all" => array()));
    }
    public function testQueryBuilderMatchAllWithBoost()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match_all', array("boost" => 1.2));
        $this->assertEquals($result->getQuery(), array("match_all" => array("boost" => 1.2)));
    }
    public function testQueryBuilderMatchNone()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match_none');
        $this->assertEquals($result->getQuery(), array("match_none" => array()));
    }
    public function testQueryBuilderMatch()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match', 'message', 'this is a test');
        $this->assertEquals($result->getQuery(), array("match" => array("message" => 'this is a test')));
    }
    public function testQueryBuilderMatchEmptyString()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match', 'message', '');
        $this->assertEquals($result->getQuery(), array("match" => array("message" => '')));
    }
    public function testQueryBuilderMatchWithOptions()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match', 'message', array("query" => 'this is a test', "operator" => 'and'));
        $this->assertEquals($result->getQuery(), array("match" => array("message" => array("query" => 'this is a test', "operator" => 'and'))));
    }
    public function testQueryBuilderMatchPhrase()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match_phrase', 'message', 'this is a test');
        $this->assertEquals($result->getQuery(), array("match_phrase" => array("message" => 'this is a test')));
    }
    public function testQueryBuilderMatchPhraseWithOptions()
    {
        $this->plan(1);
        $result = queryBuilder()->query('match_phrase', 'message', array("query" => 'this is a test', "analyzer" => 'my_analyzer'));
        $this->assertEquals($result->getQuery(), array("match_phrase" => array("message" => array("query" => 'this is a test', "analyzer" => 'my_analyzer'))));
    }
    public function testQueryBuilderCommon()
    {
        $this->plan(1);
        $result = queryBuilder()->query('common', 'body', array("query" => 'this is bonsai cool', "cutoff_frequency" => 0.001));
        $this->assertEquals($result->getQuery(), array("common" => array("body" => array("query" => 'this is bonsai cool', "cutoff_frequency" => 0.001))));
    }
    public function testQueryBuilderCommon2()
    {
        $this->plan(1);
        $result = queryBuilder()->query('common', 'body', array("query" => 'this is bonsai cool', "cutoff_frequency" => 0.001));
        $this->assertEquals($result->getQuery(), array("common" => array("body" => array("query" => 'this is bonsai cool', "cutoff_frequency" => 0.001))));
    }
    public function testQueryBuilderQueryString()
    {
        $this->plan(1);
        $result = queryBuilder()->query('query_string', 'query', 'this AND that OR thus');
        $this->assertEquals($result->getQuery(), array("query_string" => array("query" => 'this AND that OR thus')));
    }
    public function testQueryBuilderQueryStringWithOptions()
    {
        $this->plan(1);
        $result = queryBuilder()->query('query_string', 'query', 'this AND that OR thus', array("fields" => array('content', 'name')));
        $this->assertEquals($result->getQuery(), array("query_string" => array("query" => 'this AND that OR thus', "fields" => array('content', 'name'))));
    }
    public function testQueryBuilderQueryStringAlternative()
    {
        $this->plan(1);
        $result = queryBuilder()->query('query_string', array("query" => 'this AND that OR thus', "fields" => array('content', 'name')));
        $this->assertEquals($result->getQuery(), array("query_string" => array("query" => 'this AND that OR thus', "fields" => array('content', 'name'))));
    }
    public function testQueryBuilderSimpleQueryString()
    {
        $this->plan(1);
        $result = queryBuilder()->query('simple_query_string', 'query', 'foo bar baz');
        $this->assertEquals($result->getQuery(), array("simple_query_string" => array("query" => 'foo bar baz')));
    }
    public function testQueryBuilderTerm()
    {
        $this->plan(1);
        $result = queryBuilder()->query('term', 'user', 'kimchy');
        $this->assertEquals($result->getQuery(), array("term" => array("user" => 'kimchy')));
    }
    public function testQueryBuilderTermWithBoost()
    {
        $this->plan(1);
        $result = queryBuilder()->query('term', 'status', array("value" => 'urgent', "boost" => '2.0'));
        $this->assertEquals($result->getQuery(), array("term" => array("status" => array("value" => 'urgent', "boost" => '2.0'))));
    }
    public function testQueryBuilderTermMultiple()
    {
        $this->plan(1);
        $result = queryBuilder()->orQuery('term', 'status', array("value" => 'urgent', "boost" => '2.0'))->orQuery('term', 'status', 'normal');
        $this->assertEquals($result->getQuery(), array("bool" => array("should" => array(array("term" => array("status" => array("value" => 'urgent', "boost" => '2.0'))), array("term" => array("status" => 'normal'))))));
    }
    public function testQueryBuilderTerms()
    {
        $this->plan(1);
        $result = queryBuilder()->query('terms', 'user', array('kimchy', 'elastic'));
        $this->assertEquals($result->getQuery(), array("terms" => array("user" => array('kimchy', 'elastic'))));
    }
    public function testQueryBuilderRange()
    {
        $this->plan(1);
        $result = queryBuilder()->query('range', 'age', array("gte" => 10));
        $this->assertEquals($result->getQuery(), array("range" => array("age" => array("gte" => 10))));
    }
    public function testQueryBuilderExists()
    {
        $this->plan(1);
        $result = queryBuilder()->query('exists', 'user');
        $this->assertEquals($result->getQuery(), array("exists" => array("field" => 'user')));
    }
    public function testQueryBuilderMissing()
    {
        $this->plan(1);
        $result = queryBuilder()->query('missing', 'user');
        $this->assertEquals($result->getQuery(), array("missing" => array("field" => 'user')));
    }
    public function testQueryBuilderPrefix()
    {
        $this->plan(1);
        $result = queryBuilder()->query('prefix', 'user', 'ki');
        $this->assertEquals($result->getQuery(), array("prefix" => array("user" => 'ki')));
    }
    public function testQueryBuilderPrefixWithBoost()
    {
        $this->plan(1);
        $result = queryBuilder()->query('prefix', 'user', array("value" => 'ki', "boost" => 2));
        $this->assertEquals($result->getQuery(), array("prefix" => array("user" => array("value" => 'ki', "boost" => 2))));
    }
    public function testQueryBuilderWildcard()
    {
        $this->plan(1);
        $result = queryBuilder()->query('wildcard', 'user', 'ki*y');
        $this->assertEquals($result->getQuery(), array("wildcard" => array("user" => 'ki*y')));
    }
    public function testQueryBuilderRegexp()
    {
        $this->plan(1);
        $result = queryBuilder()->query('regexp', 'name.first', 's.*y');
        $this->assertEquals($result->getQuery(), array("regexp" => array("name.first" => 's.*y')));
    }
    public function testQueryBuilderFuzzy()
    {
        $this->plan(1);
        $result = queryBuilder()->query('fuzzy', 'user', 'ki');
        $this->assertEquals($result->getQuery(), array("fuzzy" => array("user" => 'ki')));
    }
    public function testQueryBuilderType()
    {
        $this->plan(1);
        $result = queryBuilder()->query('type', 'value', 'my_type');
        $this->assertEquals($result->getQuery(), array("type" => array("value" => 'my_type')));
    }
    public function testQueryBuilderIds()
    {
        $this->plan(1);
        $result = queryBuilder()->query('ids', 'type', 'my_ids', array("values" => array('1', '4', '100')));
        $this->assertEquals($result->getQuery(), array("ids" => array("type" => 'my_ids', "values" => array('1', '4', '100'))));
    }
    public function testQueryBuilderConstantScore()
    {
        $this->plan(1);
        $result = queryBuilder()->query('constant_score', array("boost" => 1.2), function (FilterAndQueryBuilder $q) {
            return $q->filter('term', 'user', 'kimchy');
        });
        $this->assertEquals($result->getQuery(), array("constant_score" => array("filter" => array("term" => array("user" => 'kimchy')), "boost" => 1.2)));
    }
    public function testQueryBuilderNested()
    {
        $this->plan(1);
        $result = queryBuilder()->query('nested', array("path" => 'obj1', "score_mode" => 'avg'), function (FilterAndQueryBuilder $q) {
            $q->query('match', 'obj1.name', 'blue');
            $q->query('range', 'obj1.count', array("gt" => 5));
        });
        $this->assertEquals($result->getQuery(), array("nested" => array("path" => 'obj1', "score_mode" => 'avg', "query" => array("bool" => array("must" => array(array("match" => array("obj1.name" => 'blue')), array("range" => array("obj1.count" => array("gt" => 5)))))))));
    }
    public function testQueryBuilderHasChild()
    {
        $this->plan(1);
        $result = queryBuilder()->query('has_child', 'type', 'blog_tag', function (FilterAndQueryBuilder $q) {
            return $q->query('term', 'tag', 'something');
        });
        $this->assertEquals($result->getQuery(), array("has_child" => array("type" => 'blog_tag', "query" => array("term" => array("tag" => 'something')))));
    }
    public function testQueryBuilderHasParent()
    {
        $this->plan(1);
        $result = queryBuilder()->query('has_parent', 'parent_tag', 'blog', function (FilterAndQueryBuilder $q) {
            return $q->query('term', 'tag', 'something');
        });
        $this->assertEquals($result->getQuery(), array("has_parent" => array("parent_tag" => 'blog', "query" => array("term" => array("tag" => 'something')))));
    }
    public function testQueryBuilderGeoBoundingBox()
    {
        $this->plan(1);
        $result = queryBuilder()->query('geo_bounding_box', 'pin.location', array("top_left" => array("lat" => 40, "lon" => -74), "bottom_right" => array("lat" => 40, "lon" => -74)), array("relation" => 'within'));
        $this->assertEquals($result->getQuery(), array("geo_bounding_box" => array("relation" => 'within', "pin.location" => array("top_left" => array("lat" => 40, "lon" => -74), "bottom_right" => array("lat" => 40, "lon" => -74)))));
    }
    public function testQueryBuilderGeoDistance()
    {
        $this->plan(1);
        $result = queryBuilder()->query('geo_distance', 'pin.location', array("lat" => 40, "lon" => -74), array("distance" => '200km'));
        $this->assertEquals($result->getQuery(), array("geo_distance" => array("distance" => '200km', "pin.location" => array("lat" => 40, "lon" => -74))));
    }
    public function testQueryBuilderGeoDistanceRange()
    {
        $this->plan(1);
        $result = queryBuilder()->query('geo_distance_range', 'pin.location', array("lat" => 40, "lon" => -74), array("from" => '100km', "to" => '200km'));
        $this->assertEquals($result->getQuery(), array("geo_distance_range" => array("from" => '100km', "to" => '200km', "pin.location" => array("lat" => 40, "lon" => -74))));
    }
    public function testQueryBuilderGeoPolygon()
    {
        $this->plan(1);
        $result = queryBuilder()->query('geo_polygon', 'person.location', array("points" => array(array("lat" => 40, "lon" => -70), array("lat" => 30, "lon" => -80), array("lat" => 20, "lon" => -90))));
        $this->assertEquals($result->getQuery(), array("geo_polygon" => array("person.location" => array("points" => array(array("lat" => 40, "lon" => -70), array("lat" => 30, "lon" => -80), array("lat" => 20, "lon" => -90))))));
    }
    public function testQueryBuilderGeohashCell()
    {
        $this->plan(1);
        $result = queryBuilder()->query('geohash_cell', 'pin', array("lat" => 13.408, "lon" => 52.5186), array("precision" => 3, "neighbors" => true));
        $this->assertEquals($result->getQuery(), array("geohash_cell" => array("pin" => array("lat" => 13.408, "lon" => 52.5186), "precision" => 3, "neighbors" => true)));
    }
    public function testQueryBuilderMoreLikeThis()
    {
        $this->plan(1);
        $result = queryBuilder()->query('more_like_this', array("fields" => array('title', 'description'), "like" => "Once upon a time", "min_term_freq" => 1, "max_query_terms" => 12));
        $this->assertEquals($result->getQuery(), array("more_like_this" => array("fields" => array('title', 'description'), "like" => "Once upon a time", "min_term_freq" => 1, "max_query_terms" => 12)));
    }
    public function testQueryBuilderTemplate()
    {
        $this->plan(1);
        $result = queryBuilder()->query('template', array("inline" => array("match" => array("text" => '{{query_string}}')), "params" => array("query_string" => 'all about search')));
        $this->assertEquals($result->getQuery(), array("template" => array("inline" => array("match" => array("text" => '{{query_string}}')), "params" => array("query_string" => 'all about search'))));
    }
    public function testQueryBuilderScript()
    {
        $this->plan(1);
        $result = queryBuilder()->query('script', 'script', array("inline" => "doc['num1'].value > 1", "lang" => 'painless'));
        $this->assertEquals($result->getQuery(), array("script" => array("script" => array("inline" => "doc['num1'].value > 1", "lang" => 'painless'))));
    }
    public function testQueryBuilderOr()
    {
        $this->plan(1);
        $result = queryBuilder()->query('or', array(array("term" => array("user" => 'kimchy')), array("term" => array("user" => 'tony'))));
        $this->assertEquals($result->getQuery(), array("or" => array(array("term" => array("user" => 'kimchy')), array("term" => array("user" => 'tony')))));
    }
    public function testQueryBuilderMinimumShouldMatchWithOneQueryIgnoresMinimum()
    {
        $this->plan(1);
        $result = queryBuilder()->orQuery('term', 'status', 'alert')->queryMinimumShouldMatch(2);
        $this->assertEquals($result->getQuery(), array("bool" => array("should" => array(array("term" => array("status" => 'alert'))))));
    }
    public function testQueryBuilderMinimumShouldMatchWithMultipleCombination()
    {
        $this->plan(1);
        $result = queryBuilder()->orQuery('term', 'status', 'alert')->orQuery('term', 'status', 'normal')->queryMinimumShouldMatch('2<-25% 9<-3');
        $this->assertEquals($result->getQuery(), array("bool" => array("should" => array(array("term" => array("status" => 'alert')), array("term" => array("status" => 'normal'))), "minimum_should_match" => '2<-25% 9<-3')));
    }
    public function testQueryBuilderMinimumShouldMatchWithMultipleQueries()
    {
        $this->plan(1);
        $result = queryBuilder()->orQuery('term', 'status', 'alert')->orQuery('term', 'status', 'normal')->queryMinimumShouldMatch(2);
        $this->assertEquals($result->getQuery(), array("bool" => array("should" => array(array("term" => array("status" => 'alert')), array("term" => array("status" => 'normal'))), "minimum_should_match" => 2)));
    }
}